<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionsTableSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(SystemsTableSeeder::class);
        $this->call(CurrencyTableSeeder::class);
    }
}
