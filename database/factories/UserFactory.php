<?php

use Faker\Generator as Faker;
use App\Components\User\Models\User;

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt('password'),
    ];
});
