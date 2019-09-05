<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('mobile')->nullable();
            $table->string('alternate_num')->nullable();

            $table->text('home_address')->nullable();
            $table->text('current_address')->nullable();
            $table->text('address')->nullable();
            
            $table->string('skype')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            
            $table->date('birth_date')->nullable();
            $table->string('guardian_name')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('account_holder_name')->nullable();
            $table->string('account_no')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_identifier_code')->nullable();
            $table->string('branch_location')->nullable();
            $table->string('tax_payer_id')->nullable();
            $table->text('note')->nullable();
            $table->string('password')->nullable();

            $table->longText('sticky_notes')->nullable();

            $table->integer('created_by')->nullable();

            $table->timestamp('last_login')->nullable();
            $table->timestamp('active')->nullable();
            $table->string('activation_key', 255)->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
