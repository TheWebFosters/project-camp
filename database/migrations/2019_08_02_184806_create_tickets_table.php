<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reference_no');
            $table->text('title');

            $table->unsignedInteger('category_id');
            $table->foreign('category_id')
                ->references('id')->on('categories');

            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->nullable();
            $table->longText('description');
            $table->enum('status', ['new', 'open', 'closed'])->default('new');

            $table->unsignedInteger('assigned_to')->nullable();
            $table->foreign('assigned_to')
                ->references('id')->on('users');
                
            $table->unsignedInteger('customer_id');
            $table->foreign('customer_id')
                ->references('id')->on('customers')
                ->onDelete('cascade');

            $table->unsignedInteger('created_by');
            $table->foreign('created_by')
                ->references('id')->on('users');

            $table->unsignedInteger('updated_by');
            $table->foreign('updated_by')
                ->references('id')->on('users');

            $table->dateTime('closed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
