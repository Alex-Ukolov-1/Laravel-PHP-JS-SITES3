<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Contract;
use App\Models\Customer;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

global $contract_codes;

$contract_codes = [];

function getRandomCode() {
    global $contract_codes;

    $year = rand(19, 20);
    $month = rand(1, 12);
    if ($month < 10) $month = '0' . $month;

    $number = rand(1, 25);
    if ($number < 10) $number = '0' . $number;

    $code = $year . $month . '-' . $number;

    if (!in_array($code, $contract_codes)) {
        $contract_codes[] = $code;
        return $code;
    } else {
        return getRandomCode();
    }
}

$factory->define(Contract::class, function (Faker $faker) {
    $code = getRandomCode();
    $customer_id = rand(1, 100);

    $name = Customer::find($customer_id)->name . ' (' . $code . ')';

    return [
        'number' => $faker->unique()->numberBetween(1000, 9000),
        'code' => $code,
        'name' => $name,
        'date' => $faker->date,
        'budget' => rand(50000, 1000000),
        'customer_id' => $customer_id,
        'user_id' => rand(2, 101),
        'status_id' => rand(1, 3),
    ];
});
