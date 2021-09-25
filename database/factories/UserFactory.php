<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->name,
        'phone' => $faker->unique()->e164PhoneNumber,
        'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$J0wP4hlrqvEvMUcPGvr.OOlHoeUjSXVgrigJUEpt7IK.q6x/WyKzy',
        'remember_token' => Str::random(60),
        'role_id' => '2',
        'status' => rand(0, 1),
    ];
});
