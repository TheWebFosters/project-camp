<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('project_id')->nullable();
            $table->foreign('project_id')
                ->references('id')->on('projects')
                ->onDelete('cascade');

            $table->string('ref_no');
            $table->integer('category_id')->nullable();

            $table->unsignedInteger('expense_for')->nullable();
            $table->foreign('expense_for')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->enum('type', ['invoice', 'expense']);
            $table->enum('status', ['draft', 'estimate', 'final']);
            $table->string('title')->nullable();

            $table->unsignedInteger('customer_id')->nullable();
            $table->foreign('customer_id')
                ->references('id')->on('customers')
                ->onDelete('cascade');

            $table->integer('contact_id')->nullable();
            $table->integer('invoice_scheme_id')->nullable();
            $table->date('transaction_date');
            $table->date('due_date')->nullable();
            $table->enum('discount_type', ['fixed', 'percentage'])->default('fixed');
            $table->decimal('discount_amount', 20, 4)->default(0.0000);
            $table->decimal('total', 20, 4);
            $table->text('terms')->nullable();
            $table->text('notes')->nullable();
            $table->enum('payment_status', ['paid', 'due', 'partial']);
            $table->integer('created_by')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
