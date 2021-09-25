<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Income;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Income::class, function (Faker $faker) {
    return [
    		'date' => $faker->date,
    		'user_id' => rand(2, 101),
    		'money' => rand(150, 1000),
        'comment' => Str::random(rand(30, 150)),
    ];
});
