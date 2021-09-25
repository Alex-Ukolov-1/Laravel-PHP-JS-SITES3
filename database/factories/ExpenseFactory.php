<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Expense;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Expense::class, function (Faker $faker) {
    return [
    		'date' => $faker->date,
    		'car_id' => rand(1, 3),
    		'user_id' => rand(2, 101),
    		'expense_category_id' => rand(1, 3),
    		'money' => rand(150, 1000),
        'comment' => Str::random(rand(30, 150)),
    ];
});
