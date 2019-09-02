<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');

            $table->unsignedInteger('customer_id')->nullable();
            $table->foreign('customer_id')
                ->references('id')->on('customers')
                ->onDelete('cascade');

            $table->enum('billing_type', ['fixed_rate', 'project_hours', 'task_hours'])->default('fixed_rate');
            $table->decimal('total_rate', 8, 2)->default(0);
            $table->decimal('price_per_hours', 8, 2)->default(0);
            $table->decimal('estimated_hours', 8, 2)->default(0);
            $table->decimal('estimated_cost', 8, 2)->default(0);
            $table->enum('status', ['not_started', 'in_progress', 'on_hold', 'cancelled', 'completed']);
            $table->integer('lead_id');
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->text('description')->nullable();
            $table->integer('created_by')->index();
            $table->boolean('favorite')->default(0);
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
        Schema::dropIfExists('projects');
    }
}
