<?php

namespace App\Notifications;

use App\System;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendReminderNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $reminder;
    public $delivery_channel;
    public function __construct($data, $delivery_channel)
    {
        $this->reminder = $data;
        $this->delivery_channel = $delivery_channel;
        $this->app_name = config('app.name');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return $this->delivery_channel;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $email_template = System::getValue('send_reminder_notification_template', true);
        $email_template = !empty($email_template) ? $email_template : [];
        $body = !empty($email_template['body']) ? $email_template['body'] : '';
        $subject = !empty($email_template['subject']) ? $email_template['subject'] : '';
        $subject = preg_replace(["/{reminder_for}/", "/{remind_to}/", "/{remind_on}/", "/{notes}/", "/{company_name}/"], [$this->reminder['remind_for'], $this->reminder['name'], $this->reminder['remind_on'], $this->reminder['notes'], $this->app_name], $subject);
        $body = preg_replace(["/{reminder_for}/", "/{remind_to}/", "/{remind_on}/", "/{notes}/", "/{company_name}/"], [$this->reminder['remind_for'], $this->reminder['name'], $this->reminder['remind_on'], $this->reminder['notes'], $this->app_name], $body);

        return (new MailMessage)
                    ->subject($subject)
                    ->view(
                        'email.email_body',
                        ['body' => $body]
                    );
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'user_id' => $this->reminder['remind_to'],
            'reminder_id' => $this->reminder['id'],
            'lead_id' => $this->reminder['lead_id']
        ];
    }
}
