<?php

use App\Components\User\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create admin account
        $AdminUser = User::create([
            'name' => 'Superadmin',
            'email' => 'admin@example.com',
            'password' => '123456',
            'remember_token' => str_random(10),
            'last_login' => \Carbon\Carbon::now(),
            'active' => \Carbon\Carbon::now(),
            'activation_key' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
        ]);
        $AdminUser->assignRole('superadmin');
    }
}
