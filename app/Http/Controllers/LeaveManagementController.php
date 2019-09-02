<?php

namespace App\Http\Controllers;

use App\Components\User\Models\User;
use App\Http\Util\CommonUtil;
use App\Leave;
use App\Notifications\LeaveApplied;
use App\Notifications\LeaveResponded;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveManagementController extends Controller
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
        if (!request()->user()->can('leaves.create')) {
            abort(403, 'Unauthorized action.');
        }

        $user = $request->user();
        $rowsPerPage = ($request->get('rowsPerPage') > 0) ? $request->get('rowsPerPage') : 0;
        $sort_by = $request->get('sort_by');
        $descending = $request->get('descending');

        if ($descending == 'false') {
            $orderby = 'asc';
        } elseif ($descending == 'true') {
            $orderby = 'desc';
        } elseif ($descending == '') {
            $orderby = 'desc';
            $sort_by = 'id';
        }

        $leaves = Leave::join('users', 'leaves.user_id', '=', 'users.id')
        ->select('start_date', 'end_date', 'status', 'reason', 'leaves.id as id', 'users.name as employee', 'user_id', 'leaves.created_at as applied_date');

        if (!$user->hasRole('superadmin')) {
            $leaves->where('leaves.user_id', $user->id);
        }

        if (!empty(request()->get('user_id'))) {
            $leaves->where('leaves.user_id', request()->get('user_id'));
        }

        if (!empty(request()->get('status'))) {
            $leaves->where('leaves.status', request()->get('status'));
        }

        if (!empty(request()->get('date')) && request()->get('date') == 'now') {
            $leaves->where('leaves.start_date', '>=', \Carbon::now())
                ->where('leaves.end_date', '>=', \Carbon::now());
        }

        if (!empty(request()->get('date')) && request()->get('date') == 'upcoming') {
            $leaves->where('leaves.start_date', '>', \Carbon::now());
        }

        if (!empty(request()->get('date')) && request()->get('date') == 'past') {
            $leaves->where('leaves.end_date', '<', \Carbon::now());
        }
        
        $leaves = $leaves->orderBy($sort_by, $orderby)
                        ->paginate($rowsPerPage);

        $status = Leave::getStatusForLeave();

        $data = ['status' => $status, 'leaves' => $leaves];

        return $this->respond($data);
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
        if (!request()->user()->can('leaves.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only('start_date', 'end_date', 'reason');
            $input['status'] = 'pending';
            $input['user_id'] = $request->user()->id;

            $leave = Leave::create($input);

            //notify admin about leave
            if (!empty($leave)) {
                $this->_notifySuperadminAboutLeaveApplied($leave);
            }

            $output = $this->respondSuccess(__('messages.applied_successfully'));
        } catch (Exception $e) {
            $output = $this->respondWentWrong($e);
        }
        return $output;
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
        if (!request()->user()->can('leaves.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $leave = Leave::find($id);

        return $this->respond($leave);
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
        if (!request()->user()->can('leaves.edit')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only('start_date', 'end_date', 'reason');

            $leave = Leave::find($id);
            $leave->update($input);

            $output = $this->respondSuccess(__('messages.updated_successfully'));
        } catch (Exception $e) {
            $output = $this->respondWentWrong($e);
        }
        return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!request()->user()->can('leaves.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $leave = Leave::find($id);

            $leave->delete();

            $output = $this->respondSuccess(__('messages.deleted_successfully'));
        } catch (Exception $e) {
            $output = $this->respondWentWrong($e);
        }
        return $output;
    }

    /**
     * save databse notification.
     *
     * @return \Illuminate\Http\Response
     */
    protected function _notifySuperadminAboutLeaveApplied($leave)
    {
        $admin = $this->CommonUtil->getSuperadmin();
        $admin->notify(new LeaveApplied($leave));
    }

    /**
     * update the status of leave.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request)
    {
        if (!request()->user()->can('leaves.edit')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $id = $request->get('id');
            $leave = Leave::findOrFail($id);

            //Log activity
            $status = $request->get('status');
            activity()
                ->performedOn($leave)
                ->withProperties(['from' => $leave->status, 'to' => $status])
                ->log('status');

            $leave->status = $status;
            $leave->disableLogging();
            $leave->update();

            DB::commit();
            
            //notify employee
            if (!empty($leave)) {
                $admin = $this->CommonUtil->getSuperadmin();
                $employee = User::find($leave->user_id);
                $employee->notify(new LeaveResponded($leave, $admin));
            }

            $output = $this->respondSuccess(__('messages.updated_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            $output = $this->respondWentWrong($e);
        }

        return $output;
    }

    /**
     * Get Filters for Leaves
     *
     * @return \Illuminate\Http\Response
     */
    public function getFilters()
    {
        $employees = User::getUsersForDropDown(true);
        $status = Leave::getStatusForLeave(true);
        $date = Leave::getDateFilter();

        $data = [
                'employees' => $employees,
                'status' => $status,
                'date' => $date
            ];

        return $this->respond($data);
    }
}
