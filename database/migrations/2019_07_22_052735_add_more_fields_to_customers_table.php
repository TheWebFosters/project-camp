<?php

use App\Customer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreFieldsToCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->integer('status_id')->after('shipping_address');
            $table->integer('source_id')->nullable()->after('status_id');
            $table->integer('assigned_to')->nullable()->after('source_id');
            $table->dateTime('contacted_date')->nullable()->after('assigned_to');
            $table->text('description')->nullable()->after('contacted_date');
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
        Schema::table('customers', function (Blueprint $table) {
            //
        });
    }
}
