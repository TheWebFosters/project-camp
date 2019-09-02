<?php

namespace App\Console\Commands;

use App\Components\User\Models\User;
use App\Customer;
use App\Notifications\SendReminderNotification;
use App\Reminder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:SendReminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email or notification to the employee';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $start = Carbon::now()->format('Y-m-d H:i:00');
        
        $end = Carbon::now()->addMinutes(5)->format('Y-m-d H:i:00');

        $reminders = Reminder::whereBetween('remind_on', [$start, $end])
                        ->get();

        foreach ($reminders as $reminder) {
            $employee = User::findOrFail($reminder->remind_to);
            $lead = Customer::findOrFail($reminder->remindable_id);
            $reminder['name'] = $employee->name;
            $reminder['lead_id'] = $lead->id;

            $delivery_channel = ['database'];
            if ($reminder->send_email && config('app.env') != 'demo') {
                $delivery_channel[] = 'mail';
            }
            
            $employee->notify(new SendReminderNotification($reminder, $delivery_channel));
        }
    }
}
