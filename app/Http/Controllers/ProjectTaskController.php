<?php

namespace App\Http\Controllers;

use App\Category;
use App\Components\User\Models\User;
use App\Http\Util\CommonUtil;
use App\Notifications\TaskCreatedNotification;
use App\Project;
use App\ProjectTask;
use App\ProjectTaskMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Notification;

class ProjectTaskController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $commonUtil;

    /**
     * Constructor.
     *
     * @param CommonUtil
     */
    public function __construct(CommonUtil $commonUtil)
    {
        $this->CommonUtil = $commonUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $project_id = request()->get('project_id', false);

        if (!empty($project_id) && (!request()->user()->can('project.'.$project_id.'.task.view'))) {
            abort(403, 'Unauthorized action.');
        }

        $view_style = request()->input('view_style');

        $user = request()->user();
        $tasks_id = $this->CommonUtil->getAssignedTasksForEmployee($user->id);

        $project_categories = [];
        $tasks = ProjectTask::with('taskCreator', 'project', 'taskMembers', 'taskMembers.media', 'category', 'notes');

        if (!empty($project_id)) {
            $tasks = $tasks->where('project_id', $project_id);
        }
        if (empty($project_id) && !$user->hasRole('superadmin')) {
            $tasks = $tasks->whereIn('id', $tasks_id);
        }

        if (!empty(request()->input('user_id'))) {
            $user_id = request()->input('user_id');
            $tasks->whereHas('taskMembers', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            });
        }

        if (!empty(request()->input('assigned_to_me'))) {
            $user_id = request()->user()->id;
            $tasks->whereHas('taskMembers', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            });
        }

        if (!empty(request()->input('status'))) {
            if ('completed' == request()->input('status')) {
                $tasks->where('is_completed', 1);
            } elseif ('over_due' == request()->input('status')) {
                $tasks->where('is_completed', 0)
                ->where('due_date', '<', \Carbon::now());
            } elseif ('incompleted' == request()->input('status')) {
                $tasks->where('is_completed', 0);
            }
        }

        if ($user->hasRole('contact')) {
            $tasks->where('show_to_customer', 1);
        }
        
        if ($view_style == 'grid') {
            $tasks = $tasks
                ->get();

            $tasks = $tasks->groupBy('category_id')->toArray();

            if (!empty($project_id)) {
                $project_categories = Category::where('project_id', $project_id)->get();
            } else {
                //If superadmin show all categories
                //else show categories of projects where user is a member
                if ($user->hasRole('superadmin')) {
                    $project_categories = Category::all();
                } else {
                    $assigned_project_ids = $this->CommonUtil->getAssignedProjectForEmployee($user->id);
                    $project_categories = Category::whereIn('project_id', $assigned_project_ids)->get();
                }
            }
        } else {
            $tasks = $tasks->latest()
                    ->simplePaginate(10);
        }

        $output = [
            'tasks' => $tasks,
            'project_categories' => $project_categories
        ];

        return $this->respond($output);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $project_id = request()->get('project_id', false);

        if (!empty($project_id) && (!request()->user()->can('project.'.$project_id.'.task.create'))) {
            abort(403, 'Unauthorized action.');
        }

        if (!empty($project_id)) {
            $project = Project::with('members')->find($project_id);
        }

        if (empty($project_id)) {
            $project = Project::select('name', 'id')
                            ->get()
                            ->toArray();
        }

        $priority = $this->CommonUtil->getPriorityList();
        $categories = Category::forDropdown('tasks', $project_id);

        $task = [
                    'priority' => $priority,
                    'project' => $project,
                    'categories' => $categories
                ];

        return $task;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $project_id = request()->get('project_id');

        if (!request()->user()->can('project.'.$project_id.'.task.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $task = $request->only(
                'project_id',
                'subject',
                'hourly_rate',
                'start_date',
                'due_date',
                'priority',
                'description',
                'show_to_customer',
                'category_id'
            );

            $task['show_to_customer'] = !empty($task['show_to_customer']) ? $task['show_to_customer'] : 0;

            $user = $request->user();
            if (($user->hasRole('contact'))) {
                $task['show_to_customer'] = 1;
            }
            $task['is_completed'] = 0;
            $task['created_by'] = $user->id;

            //TODO: Find better way of generating task id.
            $project_task = ProjectTask::create($task);

            //required data for database notification
            $notification_data = [
                            'task_id' => $project_task->id,
                            'project_id' => $task['project_id'],
                        ];

            //Generate task id and update it.
            $task_id = $this->CommonUtil->generateTaskId($project_task);
            $project_task->disableLogging();
            $project_task->task_id = $task_id;
            $project_task->update();

            $task_members = $request->input('user_id');
            if (!empty($task_members)) {
                $project_task->taskMembers()->sync($task_members);
            }

            $this->_saveTaskCreatedNotifications($task_members, $notification_data);

            DB::commit();

            $output = $this->respondSuccess(__('messages.saved_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            $output = $this->respondWentWrong($e);
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $project_id = request()->get('project_id');

        if (!request()->user()->can('project.'.$project_id.'.task.view')) {
            abort(403, 'Unauthorized action.');
        }

        $task = ProjectTask::where('project_id', $project_id)
                            ->with('taskCreator', 'project', 'taskMembers')
                            ->find($id);

        return $task;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $project_id = request()->get('project_id');

        if (!request()->user()->can('project.'.$project_id.'.task.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $task = ProjectTask::find($id);
        $task_members = ProjectTaskMember::where('project_task_id', $id)
                                ->pluck('user_id');

        $project = Project::with('members')->find($project_id);
        $priority = $this->CommonUtil->getPriorityList();
        $categories = Category::forDropdown('tasks', $project_id);

        $data = [
                    'priority' => $priority,
                    'task' => $task,
                    'task_members' => $task_members,
                    'project' => $project,
                    'categories' => $categories
                ];

        return $data;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $project_id = request()->get('project_id');

        if (!request()->user()->can('project.'.$project_id.'.task.edit')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $input = $request->only('subject', 'hourly_rate', 'priority', 'description', 'show_to_customer', 'due_date', 'start_date', 'category_id');

            $project_task = ProjectTask::where('project_id', $project_id)
            ->find($id);

            $project_task->update($input);

            $task_members = $request->input('user_id');
            if (!empty($task_members)) {
                $members = $project_task->taskMembers()->sync($task_members);
            }

            //send notification to newly added members
            if (!empty($members['attached'])) {
                //required data for database notification
                $notification_data = [
                            'task_id' => $id,
                            'project_id' => $project_id,
                        ];
                $this->_saveTaskCreatedNotifications($members['attached'], $notification_data);
            }

            DB::commit();

            $output = $this->respondSuccess(__('messages.updated_successfully'));
        } catch (Exception $e) {
            $output = $this->respondWentWrong($e);
        }

        return $output;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function updateDescription()
    {
        $project_id = request()->get('project_id');

        if (!request()->user()->can('project.'.$project_id.'.task.edit')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = request()->only('description');
            
            $id = request()->get('id');
            $project_task = ProjectTask::where('project_id', $project_id)
            ->findOrFail($id);

            $project_task->description = $input['description'];
            $project_task->update();

            $output = $this->respondSuccess(__('messages.saved_successfully'), ['task' => $project_task]);
        } catch (\Exception $e) {
            $output = $this->respondWentWrong($e);
        }

        return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $project_id = request()->get('project_id');

        if (!request()->user()->can('project.'.$project_id.'.task.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $project_task = ProjectTask::find($id);

            $project_task->delete();

            ProjectTaskMember::where('project_task_id', $id)
                        ->delete();

            $output = $this->respondSuccess(__('messages.deleted_successfully'));
        } catch (Exception $e) {
            $output = $this->respondWentWrong($e);
        }

        return $output;
    }

    /**
     * Mark the task as completed.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function markAsCompleted()
    {
        $project_id = request()->get('project_id');

        if (!request()->user()->can('project.'.$project_id.'.task.edit')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $task_id = request()->get('taskId');
            $is_completed = request()->get('is_completed');

            if ('true' == $is_completed) {
                $is_completed = 1;
            } elseif ('false' == $is_completed) {
                $is_completed = 0;
            }

            $project_task = ProjectTask
                            ::where('project_id', $project_id)
                            ->findOrFail($task_id);
            $project_task->is_completed = $is_completed;
            $project_task->save();

            $output = $this->respondSuccess(__('messages.updated_successfully'));
        } catch (\Exception $e) {
            $output = $this->respondWentWrong($e);
        }

        return $output;
    }

    /**
     * save databse notification.
     *
     * @return \Illuminate\Http\Response
     */
    protected function _saveTaskCreatedNotifications($members, $notification_data)
    {
        $notifiable_users = User::find($members);

        Notification::send($notifiable_users, new TaskCreatedNotification($notification_data));
    }

    public function getFilterData($project_id)
    {
        $project_id = in_array($project_id, [0, 'null']) ? 0 : $project_id;
        $data = [];
        if (!request()->user()->can('superadmin') && !request()->user()->hasRole('lead.'.$project_id)) {
            $data['members'] = null;
        }

        $project = null;
        if (!empty($project_id)) {
            $project = Project::with('members')->find($project_id);
        }

        if (!empty($project)) {
            $data['members'] = $project->members;
        } else {
            $data['members'] = null;
        }

        $data['projects'] = null;
        if (request()->user()->can('superadmin')) {
            $data['members'] = User::get();
        }

        if (empty($project_id)) {
            if (request()->user()->can('superadmin')) {
                $data['projects'] = Project::get();
            } else {
                $user_id = request()->user()->id;
                $data['projects'] = Project::whereHas('members', function ($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                })->get();
            }
        }


        return $data;
    }
}
