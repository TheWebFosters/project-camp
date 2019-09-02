<?php

namespace App\Notifications;

use App\System;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomersContactAdded extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    public $name;
    public $email;
    public $password;
    public $customer;

    public function __construct($data)
    {
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->password = $data['password'];
        $this->customer = $data['customer'];
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $email_template = System::getValue('customers_contact_added_template', true);

        $email_template = !empty($email_template) ? $email_template : [];
        $body = !empty($email_template['body']) ? $email_template['body'] : '';
        $subject = !empty($email_template['subject']) ? $email_template['subject'] : '';

        $subject = preg_replace(["/{contact_name}/", "/{customer_name}/", "/{email}/", "/{password}/", "/{login_link}/", "/{company_name}/"], [$this->name, $this->customer, $this->email, $this->password,  url('/login'), $this->app_name], $subject);
        $body = preg_replace(["/{contact_name}/", "/{customer_name}/", "/{email}/", "/{password}/", "/{login_link}/", "/{company_name}/"], [$this->name, $this->customer, $this->email, $this->password,  url('/login'), $this->app_name], $body);

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
            //
        ];
    }
}
