<?php

namespace App\Notifications;

use App\System;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendInvoiceToCustomer extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    public $invoice_number;
    public $app_name;
    public $customer;
    public $invoice_name;
    public $pdf;
    public function __construct($invoice, $customer, $pdf)
    {
        $this->invoice_number = $invoice->ref_no;
        $this->customer = $customer->company;
        $this->pdf = $pdf;
        $this->invoice_name = $invoice->invoice_name;
        $this->app_name = config('app.name');
        $this->due_date = $invoice->due_date;
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
        $email_template = System::getValue('send_invoice_to_customer_template', true);
        $email_template = !empty($email_template) ? $email_template : [];
        $body = !empty($email_template['body']) ? $email_template['body'] : '';
        $subject = !empty($email_template['subject']) ? $email_template['subject'] : '';

        $subject = preg_replace(["/{invoice_number}/", "/{customer_name}/", "/{due_date}/", "/{company_name}/"], [$this->invoice_number, $this->customer, $this->due_date, $this->app_name], $subject);

        $body = preg_replace(["/{invoice_number}/", "/{customer_name}/", "/{due_date}/", "/{company_name}/"], [$this->invoice_number, $this->customer, $this->due_date, $this->app_name], $body);

        return (new MailMessage)
                ->subject($subject)
                ->view(
                    'email.email_body',
                    ['body' => $body]
                )
                ->attachData($this->pdf->output(), $this->invoice_name);
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
