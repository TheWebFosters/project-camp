<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceReminder extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    public $subject;
    public $body;
    public $invoice_name;
    public $pdf;
    
    public function __construct($data)
    {
        $this->subject = $data['subject'];
        $this->body = $data['body'];
        $this->invoice_name = !empty($data['name']) ? $data['name'] : '';
        $this->pdf = !empty($data['pdf']) ? $data['pdf'] : '';
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
        $mailMessage = new MailMessage();

        $mailMessage
            ->subject($this->subject)
            ->line($this->body);

        if (!empty($this->pdf)) {
            $mailMessage->attachData($this->pdf->output(), $this->invoice_name);
        }

        return $mailMessage;
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
