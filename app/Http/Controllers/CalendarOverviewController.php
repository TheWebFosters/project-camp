<?php

namespace App\Http\Controllers;

use App\Http\Util\CommonUtil;
use App\Leave;
use App\Project;
use App\ProjectTask;
use App\Transaction;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class CalendarOverviewController extends Controller
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
        try {
            $user = $request->user();
            $start = $request->get('start');
            $end = $request->get('end');

            //Projects
            $project_ids = $this->CommonUtil->getAssignedProjectForEmployee($user->id);
            $projects = new Project();
            if (!$user->hasRole('superadmin')) {
                $projects = $projects->orWhere('projects.lead_id', $user->id)
                ->orWhereIn('projects.id', $project_ids);
            }

            $projects = $projects->whereBetween('end_date', [$start, $end])
                ->get()->toArray();

            //Projects tasks
            $project_task = new ProjectTask();
            if (!$user->hasRole('superadmin')) {
                $project_task = $project_task->whereHas('taskMembers', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                });
            }

            $project_task = $project_task->whereBetween('due_date', [$start, $end])
                ->get()->toArray();

            //Projects transactions
            if ($user->hasRole('superadmin')) {
                $transaction_info = [];
                $transactions = Transaction::OfTransaction('invoice')
                                ->whereBetween('due_date', [$start, $end])
                                ->get()
                                ->toArray();

                foreach ($transactions as $transaction) {
                    $transaction['name'] = $transaction['title'];
                    $transaction['date'] = \Carbon::parse($transaction['due_date'])->toDateString();
                    $transaction['type'] = 'invoice';
                    $transaction_info[] = $transaction;
                }
            }

            //leaves
            $leaves = Leave::where('user_id', $user->id)
                            ->where('status', 'approved')
                            ->whereBetween('end_date', [$start, $end])
                            ->get();
            
            $dates = [];
            $reasons = [];
            foreach ($leaves as $leave) {
                $start = \Carbon::parse($leave->start_date)->toDateString();
                $end = \Carbon::parse($leave->end_date)->toDateString();
                $periods = CarbonPeriod::create($start, $end);
                $reason = $leave->reason;
                foreach ($periods as $key => $date) {
                    $dates [] = $date->format('Y-m-d');
                    $reasons[] = $reason;
                }
            }

            $leave_date = [];
            if (!empty($dates)) {
                foreach ($dates as $key => $date) {
                    $leave_date[$key]['date'] = $date;
                    $leave_date[$key]['type'] = 'leave';
                    $leave_date[$key]['name'] = $reasons[$key];
                }
            }
            
            $project_info = [];
            foreach ($projects as $project) {
                $project['date'] = \Carbon::parse($project['end_date'])->toDateString();
                $project['type'] = 'project';
                $project_info[] = $project;
            }

            $tasks = [];
            foreach ($project_task as $task) {
                $task['type'] = 'task';
                $task['name'] = $task['subject'];
                $task['date'] = \Carbon::parse($task['due_date'])->toDateString();
                $tasks[] = $task;
            }

            $calendar_data = array_merge($project_info, $tasks);

            if (!empty($transaction_info)) {
                $calendar_data = array_merge($calendar_data, $transaction_info);
            }

            if (!empty($leave_date)) {
                $calendar_data = array_merge($leave_date, $calendar_data);
            }
            
            $output = $calendar_data;
        } catch (Exception $e) {
            $output = $this->respondWentWrong($e);
        }
        
        return $output;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
