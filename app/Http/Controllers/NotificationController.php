<?php

namespace App\Http\Controllers;

use App\Components\User\Models\User;
use App\Customer;
use App\Leave;
use App\Project;
use App\ProjectTask;
use App\Reminder;
use App\Ticket;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = request()->user();
        $notifications_count = $user->unreadNotifications->count();
        return $this->respond($notifications_count);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
    }

    /**
     * Mark the notifications
     * as read.
     *
     * @param  \Illuminate\Http\Response
     *
     * @return \Illuminate\Http\Response
     */
    public function markAsRead()
    {
        $user = request()->user();
        $notifications = $user->notifications()
                            ->orderBy('created_at', 'desc')
                            ->simplePaginate();

        $user->notifications()
                ->orderBy('created_at', 'desc')
                ->simplePaginate()->markAsRead();

        foreach ($notifications as $notification) {
            if ('App\Notifications\ProjectCreatedNotification' == $notification->type) {
                $notification['project'] = Project::with('creator')
                            ->findOrFail($notification->data['project_id']);
            } elseif ('App\Notifications\TaskCreatedNotification' == $notification->type) {
                $notification['task'] = ProjectTask::with('taskCreator', 'project')
                                ->findOrFail($notification->data['task_id']);
            } elseif ('App\Notifications\LeaveApplied' == $notification->type) {
                $notification['applicant'] = User::findOrFail($notification->data['user_id']);
                $notification['leave'] = Leave::findOrFail($notification->data['leave_id']);
            } elseif ('App\Notifications\LeaveResponded' == $notification->type) {
                $notification['leave_answered'] = Leave::findOrFail($notification->data['leave_id']);
                $notification['answered_by'] = User::findOrFail($notification->data['admin']);
            } elseif ('App\Notifications\SendReminderNotification' == $notification->type) {
                $notification['reminder'] = Reminder::findOrFail($notification->data['reminder_id']);
                $notification['remind_to'] = User::findOrFail($notification->data['user_id']);
                $notification['lead'] = Customer::findOrFail($notification->data['lead_id']);
            } elseif ('App\Notifications\TicketCreated' == $notification->type) {
                $notification['ticket'] = Ticket::findOrFail($notification->data['ticket_id']);
                $notification['created_by'] = User::findOrFail($notification->data['created_by']);
            }
        }

        return $this->respond($notifications);
    }
}
