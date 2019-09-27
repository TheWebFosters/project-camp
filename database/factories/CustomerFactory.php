<?php

use App\Customer;
use Faker\Generator as Faker;

$factory->define(Customer::class, function (Faker $faker) {
    return [
        'company' => 'Test',
        'currency_id' => 1,
        'mobile' => '123-456-5555',
        'email' => 'test@example.com',
        'status_id' => 1,
    ];
});
