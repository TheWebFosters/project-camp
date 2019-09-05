<?php

use App\Customer;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('primary_contact_id')->nullable();
            $table->string('company');
            $table->integer('currency_id');
            $table->string('tax_number')->nullable();
            $table->string('mobile');
            $table->string('alternate_contact_no')->nullable();
            $table->string('email');
            $table->string('website')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('country')->nullable();
            $table->text('billing_address')->nullable();
            $table->text('shipping_address')->nullable();

            $table->integer('status_id');
            $table->integer('source_id')->nullable();
            $table->integer('assigned_to')->nullable();
            $table->dateTime('contacted_date')->nullable();
            $table->text('description')->nullable();

            $table->integer('created_by')->index();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('customer_id')->after('created_by')->nullable();
            $table->foreign('customer_id')
                ->references('id')->on('customers')
                ->onDelete('cascade');
        });

        //assign status 1 to existing customer.
        $customers = Customer::where('status_id', '!=', null)
                            ->update(['status_id' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
