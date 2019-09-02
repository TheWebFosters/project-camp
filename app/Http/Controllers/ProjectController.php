<?php

namespace App\Http\Controllers;

use App\Category;
use App\Components\User\Models\User;
use App\Customer;
use App\Http\Util\CommonUtil;
use App\Notifications\ProjectCreatedNotification;
use App\Project;
use App\ProjectMember;
use App\ProjectMilestone;
use App\ProjectTask;
use App\ProjectTaskMember;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Notification;
use Spatie\Permission\Models\Role;

class ProjectController extends Controller
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
    public function index(Request $request)
    {
        $user = $request->user();

        $projects = Project::with('customer', 'categories', 'members', 'members.media');

        if (!$user->hasRole('superadmin')) {
            //If employee then get projects assigned or lead.
            if ($user->is_employee) {
                $project_ids = $this->CommonUtil->getAssignedProjectForEmployee($user->id);
                $projects->orWhere('projects.lead_id', $user->id)
                    ->orWhereIn('projects.id', $project_ids);
            }

            //If customer, then get project for that customer.
            if ($user->is_client) {
                $projects->Where('customer_id', $user->customer_id);
            }
        }

        $projects = $projects->withCount(['tasks',
                        'tasks as completed_task' => function ($query) {
                            $query->where('is_completed', 1);
                        },
                        'tasks as overdue_task' => function ($query) use ($user) {
                            $query->where('is_completed', 0)
                            ->where('due_date', '<', \Carbon::now())
                            ->whereHas('taskMembers', function ($q) use ($user) {
                                $q->where('user_id', $user->id);
                            });
                        }]);

        if (!empty($request->input('status'))) {
            $projects->where('status', $request->input('status'));
        }
        if (!empty($request->input('category_id'))) {
            $category_id = $request->input('category_id');
            $projects->whereHas('categories', function ($q) use ($category_id) {
                $q->where('category_id', $category_id);
            });
        }
        if (!empty($request->input('customer_id'))) {
            $projects->where('customer_id', $request->input('customer_id'));
        }
        
        $result = $projects->latest()
                    ->simplePaginate(10);

        //Append is_favorited to each project and avatar
        foreach ($result as $key => $val) {
            $result[$key] = $val->append('is_favorited');
        }
        
        $status = Project::getStatusForProject();

        $data = ['status' => $status, 'projects' => $result];

        return $data;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!request()->user()->can('project.create')) {
            abort(403, 'Unauthorized action.');
        }

        $customers = Customer::getCustomersForDropDown();
        $users = User::getUsersForDropDown();
        $billingTypes = Project::getBillingTypes();
        $status = Project::getStatusForProject();
        $categories = Category::forDropdown('projects');

        $project = [
                'customers' => $customers,
                'users' => $users,
                'billingTypes' => $billingTypes,
                'status' => $status,
                'categories' => $categories,
            ];

        return $project;
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
        if (!request()->user()->can('project.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            //TODO: optimise the process.
            DB::beginTransaction();

            $project_data = $request->only(
                'name',
                'customer_id',
                'billing_type',
                'total_rate',
                'price_per_hours',
                'estimated_hours',
                'estimated_cost',
                'status',
                'lead_id',
                'description',
                'start_date',
                'end_date'
            );

            $project_data['created_by'] = $request->user()->id;
            $project = Project::create($project_data);

            //Add members
            $project_members = $request->input('user_id');
            array_push($project_members, $project_data['lead_id']);
            $project->members()->sync($project_members);

            //Add category
            $category = $request->input('category_id');
            $project->categories()->sync($category);

            $roles = $this->CommonUtil->createRoleAndPermissionsForProject($project->id);

            //Assign project member role
            $users = User::find($project_members);
            foreach ($users as $user) {
                $user->assignRole($roles['member']);
            }
            //Assign lead role
            $project_lead = User::find($project_data['lead_id']);
            $project_lead->assignRole($roles['lead']);

            //Assign roles to customer contacts
            if (!empty($project_data['customer_id'])) {
                $contacts = Customer::find($project_data['customer_id'])
                                ->contacts;
                foreach ($contacts as $contact) {
                    $contact->assignRole($roles['customer']);
                }
            }

            DB::commit();

            $this->_saveProjectCreatedNotifications($project_members, $project->id);

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
        if (!request()->user()->can('project.'.$id.'.overview')) {
            abort(403, 'Unauthorized action.');
        }

        $project = Project::with('customer', 'lead', 'lead.media', 'tasks', 'categories', 'members', 'members.media')
                            ->withCount(['tasks',
                                'tasks as completed_task' => function ($query) {
                                    $query->where('is_completed', 1);
                                }, ])
                            ->find($id);

        $task_count = ProjectTask::where('project_id', $id)
                        ->where('is_completed', 0)
                        ->count();

        $milestone_count = ProjectMilestone::where('project_id', $id)
                                            ->count();

        $invoice_count = Transaction::OfTransaction('invoice')
                                ->where('project_id', $id)
                                ->where('status', 'final')
                                ->count();
        //chart data
        $project_task = ProjectTask::where('project_id', $id)
                    ->where('created_at', '>=', Carbon::now()->startOfMonth())
                    ->where('created_at', '<=', Carbon::now()->endOfMonth())
                    ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(id) as task'))
                    ->groupBy('date')
                    ->get();

        $x_axis = [];
        $y_axis = [];
        foreach ($project_task as $task) {
            $x_axis[] = $task->date;
            $y_axis[] = $task->task;
        }

        $project_overview = [
                             'project' => $project,
                             'task' => $task_count,
                             'x_axis' => $x_axis,
                             'y_axis' => $y_axis,
                             'milestone' => $milestone_count,
                             'invoice' => $invoice_count,
                            ];

        return $project_overview;
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
        if (!request()->user()->can('project.'.$id.'.edit')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $project = Project::with('categories')->findOrFail($id);

            $project_category_id = [];
            foreach ($project->categories as $category) {
                $project_category_id[] = $category->id;
            }

            $project_members = $this->getMembers($id);
            $customers = Customer::getCustomersForDropDown();
            $users = User::getUsersForDropDown();
            $billingTypes = Project::getBillingTypes();
            $status = Project::getStatusForProject();
            $categories = Category::forDropdown('projects');

            $output = [
                    'customers' => $customers,
                    'project' => $project,
                    'users' => $users,
                    'billingTypes' => $billingTypes,
                    'status' => $status,
                    'categories' => $categories,
                    'project_members' => $project_members,
                    'project_category_id' => $project_category_id,
                ];
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
    public function update(Request $request, $id)
    {
        if (!request()->user()->can('project.'.$id.'.edit')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            //TODO: optimise the process.
            DB::beginTransaction();

            $input = $request->only(
                'name',
                'customer_id',
                'billing_type',
                'total_rate',
                'price_per_hours',
                'estimated_hours',
                'estimated_cost',
                'status',
                'lead_id',
                'description',
                'start_date',
                'end_date'
            );

            $project = Project::with('members')->findOrFail($id);

            $project_lead = $project->lead_id;
            $customer = $project->customer_id;
            $members = $project->members;
            $project->update($input);

            //Update project members
            $project_members = $request->input('user_id');
            array_push($project_members, $input['lead_id']);
            $updated_members = $project->members()->sync($project_members);

            //remove task if member is removed.
            if (!empty($updated_members['detached'])) {
                ProjectTaskMember::join('project_tasks as pt', 'project_task_members.project_task_id', '=', 'pt.id')
                ->where('pt.project_id', $id)
                ->whereIn('user_id', $updated_members['detached'])
                ->delete();
            }

            //Update category
            $category = $request->input('category_id');
            $project->categories()->sync($category);

            //remove role of existing member
            $member_role = $this->CommonUtil->memberProjectRole($id);
            foreach ($members as $member) {
                $member->removeRole($member_role);
            }

            //Assign member role to newly added member
            $users = User::find($project_members);
            foreach ($users as $user) {
                $user->assignRole($member_role);
            }

            //Assign/remove lead role
            if ($project_lead != $input['lead_id']) {
                $lead_role = $this->CommonUtil->leadProjectRole($id);
                //remove role of existing lead
                $to_be_removed_project_lead = User::find($project_lead);
                $to_be_removed_project_lead->removeRole($lead_role);

                //assign role to lead
                $to_be_added_project_lead = User::find($input['lead_id']);
                $to_be_added_project_lead->assignRole($lead_role);

                array_push($updated_members['attached'], $input['lead_id']);
            }

            $customer_role = $this->CommonUtil->customerProjectRole($id);

            //remove roles of contacts
            if (!empty($customer)) {
                $to_be_removed_contacts = Customer::find($customer)
                            ->contacts;
                foreach ($to_be_removed_contacts as $contact) {
                    $contact->removeRole($customer_role);
                }
            }

            //Assign roles to customer contacts
            if (!empty($input['customer_id'])) {
                $to_be_added_contacts = Customer::find($input['customer_id'])
                    ->contacts;
                foreach ($to_be_added_contacts as $contact) {
                    $contact->assignRole($customer_role);
                }
            }

            DB::commit();
            //notify only newly assigned members
            if (!empty($updated_members['attached'])) {
                $this->_saveProjectCreatedNotifications($updated_members['attached'], $id);
            }

            $output = $this->respondSuccess(__('messages.updated_successfully'));
        } catch (Exception $e) {
            DB::rollBack();
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
        if (!request()->user()->can('project.'.$id.'.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            Project::destroy($id);

            ProjectMember::where('project_id', $id)
                        ->delete();

            $output = $this->respondSuccess(__('messages.deleted_successfully'));
        } catch (\Exception $e) {
            $output = $this->respondWentWrong($e);
        }

        return $output;
    }

    /**
     * Mark the specified project As Favorite.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function markAsFavorite(Request $request)
    {
        try {
            $id = $request->get('id');
            $project = Project::findOrFail($id);

            $favorite = $request->get('favorite');
            if ('false' == $favorite) {
                $project->favorite();
                $favorite = true;
                $msg = __('messages.favorited');
            } elseif ('true' == $favorite) {
                $project->unfavorite();
                $favorite = false;
                $msg = __('messages.unfavorited');
            }

            $output = $this->respondSuccess($msg, ['favorite' => $favorite]);
        } catch (\Exception $e) {
            $output = $this->respondWentWrong($e);
        }

        return $output;
    }

    /**
     * update the status of project.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request)
    {
        $id = $request->get('id');

        if (!request()->user()->can('project.'.$id.'.status')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $project = Project::findOrFail($id);

            $status = $request->get('status');
            //Log activity
            activity()
                ->performedOn($project)
                ->withProperties(['from' => $project->status, 'to' => $status])
                ->log('status');

            $project->status = $status;
            $project->disableLogging();
            $project->update();

            DB::commit();

            $output = $this->respondSuccess(__('messages.updated_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            $output = $this->respondWentWrong($e);
        }

        return $output;
    }

    /**
     * Get Project Members.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function getMembers($id)
    {
        $project_members = ProjectMember::where('project_id', $id)
                            ->pluck('user_id');

        if ('task' == request()->get('type')) {
            $project_members = User::whereIn('id', $project_members)
                                ->select('id', 'name')
                                ->get()
                                ->toArray();
        }

        return $project_members;
    }

    /**
     * save databse notification.
     *
     * @return \Illuminate\Http\Response
     */
    protected function _saveProjectCreatedNotifications($members, $project_id)
    {
        $notifiable_users = User::find($members);
        Notification::send($notifiable_users, new ProjectCreatedNotification($project_id));
    }

    /**
     * Get Project List.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return $projects
     */
    public function getProjectsList()
    {
        $projects = Project::select('id', 'name')
                        ->get()
                        ->toArray();

        return $projects;
    }

    /**
     * Get Customer for the
     * Project.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return $customer_id
     */
    public function getCustomerId($id)
    {
        $project = Project::find($id);

        return $project;
    }

    /**
     * get project statistics
     *
     * @return $project_statistics
     */
    public function getStatistics()
    {
        if (!request()->user()->can('project.create')) {
            abort(403, 'Unauthorized action.');
        }
        
        $project_statistics = Project::select(
            DB::raw("COALESCE(SUM(IF(status = 'not_started', 1, 0)), 0) as not_started"),
            DB::raw("COALESCE(SUM(IF(status = 'in_progress', 1, 0)), 0) as in_progress"),
            DB::raw("COALESCE(SUM(IF(status = 'on_hold', 1, 0)), 0) as on_hold"),
            DB::raw("COALESCE(SUM(IF(status = 'cancelled', 1, 0)), 0) as cancelled"),
            DB::raw("COALESCE(SUM(IF(status = 'completed', 1, 0)), 0) as completed"),
            DB::raw('count(*) as total')
            )
            ->first();

        return $this->respond($project_statistics);
    }
}
