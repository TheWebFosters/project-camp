<?php

use App\System;
use Illuminate\Database\Migrations\Migration;

class AddSystemSettingsRows extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        System::insert([
            ['key' => 'first_day_of_week', 'value' => 0],
            ['key' => 'address_line_1', 'value' => ''],
            ['key' => 'address_line_2', 'value' => '']
        ]);
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
