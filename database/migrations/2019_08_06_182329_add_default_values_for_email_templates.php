<?php

use App\System;
use Illuminate\Database\Migrations\Migration;

class AddDefaultValuesForEmailTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $default_values = [
            'employee_added_template' => '{"subject":"Welcome {employee_name}","body":"<p>Dear {employee_name}<\/p><p><br><\/p><p>Welcome to {company_name}<\/p><p><br><\/p><p>Please find your login details below:<\/p><p>Email: {email}<\/p><p>Password: {password}<\/p><p>Login link: {login_link}<\/p><p><br><\/p><p>Thank you!<\/p>","attachment":0}',
            'send_reminder_notification_template' => '{"subject":"New reminder for you!","body":"<p>Hello {remind_to}<\/p><p><br><\/p><p>You have a new reminder: {reminder_for}<\/p><p>Notes: {notes}<\/p><p>Datetime: {remind_on}<\/p>","attachment":0}',
            'customer_added_template' => '{"subject":"Welcome {customer_name}","body":"<p>Dear {customer_name}<\/p><p><br><\/p><p>Welcome to {company_name}, its our pleasure to have you with us.<\/p>","attachment":0}',
            'customers_contact_added_template' => '{"subject":"Welcome {contact_name}","body":"<p>Dear {contact_name}<\/p><p><br><\/p><p>Welcome to {company_name}<\/p><p><br><\/p><p>Please find your login details below:<\/p><p>Email: {email}<\/p><p>Password: {password}<\/p><p>Login link: {login_link}<\/p><p><br><\/p><p>Thank you!<\/p>","attachment":0}',
            'send_invoice_to_customer_template' => '{"subject":"{invoice_number} from {company_name}","body":"<p>Dear {customer_name}<\/p><p><br><\/p><p>Please find attached the invoice {invoice_number}.<\/p><p>Please process it as per your convenience &amp; let us know.<\/p><p><br><\/p><p>Thank you.<\/p>","attachment":1}',
            'invoice_reminder_template' => '{"subject":"{invoice_number} from {company_name}","body":"<p>Dear {customer_name}<\/p><p><br><\/p><p>This is a reminder for invoice {invoice_number}.<\/p><p>Please process it soon.<\/p><p><br><\/p><p>Thank you.<\/p>","attachment":1}',
            'lead_added_template' => '{"subject":"Welcome {lead_name}","body":"<p>Dear {lead_name}<\/p><p><br><\/p><p>Welcome to {company_name}, its our pleasure to have you with us.<\/p>","attachment":0}'
        ];

        foreach ($default_values as $key => $value) {
            System::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
