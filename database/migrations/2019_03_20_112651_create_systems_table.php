<?php

use App\System;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('systems', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key');
            $table->text('value')->nullable();
        });

        $data = [
                    'invoice_reminder_email_subject' => 'Invoice Reminder from {business_name}',
                    'invoice_reminder_email_body' => '<p>Dear {customer_name},</p>

                                    <p>Your invoice number for {project_name} is {invoice_number} and due date for this is {due_date}<br />
                                    
                                    <p>Thank you for the business.</p>
                                    <p><br></p>
                                    <p>Regards,</p> <br>
                                    <p>{business_name}</p>',
                    'invoice_reminder_attachment' => 1
                ];

        $invoice_reminder_template = json_encode($data);

        System::updateOrCreate(['key' => 'invoice_reminder_template'], ['value' => $invoice_reminder_template]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('systems');
    }
}
