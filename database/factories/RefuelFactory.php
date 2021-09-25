<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Refuel;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Refuel::class, function (Faker $faker) {
    return [
        'date' => $faker->dateTime,
				'car_id' => rand(1, 3),
				'user_id' => rand(2, 101),
				'fuel' => rand(3, 10),
				'money' => rand(150, 1000),
				'payment_type_id' => rand(1, 3),
        'comment' => Str::random(rand(30, 150)),
    ];
});
