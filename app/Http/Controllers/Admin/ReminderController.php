<?php

namespace App\Http\Controllers\Admin;

use App\Components\User\Models\User;
use App\Customer;
use App\Http\Controllers\Controller;
use App\Reminder;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $rowsPerPage = ($request->get('rowsPerPage') > 0) ? $request->get('rowsPerPage') : 0;
        $sort_by = $request->get('sort_by');
        $descending = $request->get('descending');
        $lead_id = $request->get('lead_id');

        if ($descending == 'false') {
            $orderby = 'asc';
        } elseif ($descending == 'true') {
            $orderby = 'desc';
        } elseif ($descending == '') {
            $orderby = 'desc';
            $sort_by = 'reminders.id';
        }

        $query = Reminder::where('remindable_id', $lead_id)
                    ->where('remindable_type', 'App\Customer')
                    ->join('users', 'reminders.remind_to', '=', 'users.id')
                    ->select('remind_for', 'remind_on', 'users.name as remind_to', 'reminders.id as id');

        $reminders = $query->orderBy($sort_by, $orderby)
                        ->paginate($rowsPerPage);

        return $this->respond($reminders);
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
        try {
            $reminder = $request->only('remind_for', 'remind_to', 'notes', 'remind_on');
            $reminder['created_by'] = $request->user()->id;
            $send_email = $request->input('send_email');
            $reminder['send_email'] = !empty($send_email) ? $send_email : 0;

            $lead_id = $request->input('lead_id');
            $lead = Customer::findOrFail($lead_id);
            $reminders = $lead->reminders()->create($reminder);

            $output = $this->respondSuccess(__('messages.saved_successfully'));
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
        $lead_id = request()->get('lead_id');

        $reminder = Reminder::where('remindable_id', $lead_id)
                            ->where('remindable_type', 'App\Customer')
                            ->where('reminders.id', $id)
                            ->leftJoin('users', 'reminders.remind_to', '=', 'users.id')
                            ->select('remind_for', 'remind_on', 'users.name as remind_to', 'notes')
                            ->first();

        return $this->respond($reminder);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $lead_id = request()->get('lead_id');
        $reminder = Reminder::where('remindable_id', $lead_id)
                            ->where('remindable_type', 'App\Customer')
                            ->findOrFail($id);

        $employees = User::getUsersForDropDown();

        $data = [
                'reminder' => $reminder,
                'employees' => $employees
            ];

        return $this->respond($data);
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
        try {
            $input = $request->only('remind_for', 'remind_to', 'notes', 'remind_on');
            $send_email = $request->input('send_email');
            $input['send_email'] = !empty($send_email) ? $send_email : 0;

            $lead_id = $request->get('lead_id');
            $reminder = Reminder::where('remindable_id', $lead_id)
                            ->where('remindable_type', 'App\Customer')
                            ->where('id', $id)
                            ->update($input);

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
        try {
            $lead_id = request()->get('lead_id');
            $reminder = Reminder::where('remindable_id', $lead_id)
                            ->where('remindable_type', 'App\Customer')
                            ->findOrFail($id);

            $reminder->delete();

            $output = $this->respondSuccess(__('messages.deleted_successfully'));
        } catch (Exception $e) {
            $output = $this->respondWentWrong($e);
        }
        return $output;
    }
}
