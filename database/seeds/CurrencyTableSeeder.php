<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies = [
          ['id' => '1','iso_name' => 'USD','symbol' => '$','created_at' => '2019-04-18 12:19:26','updated_at' => '2019-04-18 12:19:26']
        ];

        DB::table('currencies')->insert($currencies);
    }
}
