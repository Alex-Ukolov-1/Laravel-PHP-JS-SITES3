<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Trip;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Trip::class, function (Faker $faker) {
    return [
        'date' => $faker->dateTime,
		'car_id' => rand(1, 3),
		'user_id' => rand(2, 101),
		'cargo_type_id' => rand(1, 3),
    	'departure_point_id' => rand(1, 3),
        'destination_id' => rand(1, 2),
		'cargo_amount' => rand(100, 450),
		'unit_type_id' => rand(1, 3),
        'comment' => Str::random(rand(30, 150)),
    ];
});
