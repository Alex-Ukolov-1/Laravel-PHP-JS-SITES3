<?php

use Illuminate\Database\Seeder;
use \App\Models\Role;
use \App\Models\User;
use \App\Models\Customer;
use \App\Models\Expense;
use \App\Models\Income;
use \App\Models\Refuel;
use \App\Models\Trip;
use \App\Models\Contract;
use \App\Models\Settings\Status;
use \App\Models\Settings\Car;
use \App\Models\Settings\ExpenseCategory;
use \App\Models\Settings\PaymentType;
use \App\Models\Settings\CargoType;
use \App\Models\Settings\DeparturePoint;
use \App\Models\Settings\Destination;
use \App\Models\Settings\IntermediatePoint;
use \App\Models\Settings\StopAndService;
use \App\Models\Settings\UnitType;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

    	// $this->call(UsersTableSeeder::class);

    	Role::insert([
    		['name' => 'Администратор'],
    		['name' => 'Водитель'],
    	]);

    	Car::insert([
    		['number' => 'Т 220 ХА 750 (MAN)'],
    		['number' => 'Т 874 ЕР 799 (MAN)'],
    		['number' => 'Н 786 КА 750 (MAN)'],
    	]);

        ExpenseCategory::insert([
            ['name' => 'Экскаваторщик'],
            ['name' => 'ГИБДД'],
            ['name' => 'Покупка зап.частей'],
        ]);

        PaymentType::insert([
            ['name' => 'Наличные'],
            ['name' => 'Банковская карта'],
            ['name' => 'Топливная карта'],
        ]);

    	CargoType::insert([
    		['name' => 'Песок'],
    		['name' => 'Щебень'],
    		['name' => 'Гравий'],
    	]);

        DeparturePoint::insert([
            ['name' => 'Карьер Веневский'],
            ['name' => 'Карьер Комлево'],
            ['name' => 'Карьер Калуга-щебень'],
        ]);

        Destination::insert([
            ['name' => 'Завод'],
            ['name' => 'Фабрика'],
        ]);

        IntermediatePoint::insert([
            ['name' => 'Промежуточный пункт №1'],
            ['name' => 'Промежуточный пункт №2'],
            ['name' => 'Промежуточный пункт №3'],
        ]);

        StopAndService::insert([
            ['name' => 'Заправка'],
            ['name' => 'Кафе'],
            ['name' => 'Автосервис'],
        ]);

        UnitType::insert([
            ['name' => 'тонна'],
            ['name' => 'центнер'],
            ['name' => 'килограмм'],
        ]);

    	User::insert([
    		[
	  			'name' => 'admin',
	  			'phone' => '79998888888',
	  			'email' => 'admin@admin.com',
	  			'password' => '$2y$10$J0wP4hlrqvEvMUcPGvr.OOlHoeUjSXVgrigJUEpt7IK.q6x/WyKzy',
	  			'remember_token' => Str::random(60),
	  			'role_id' => '1',
	  			'status' => '1',
    		],
    	]);

        Status::insert([
            ['name' => 'Подготовка', 'type_id' => '1'],
            ['name' => 'Заключён', 'type_id' => '1'],
            ['name' => 'Исполнен', 'type_id' => '1'],
        ]);

        factory(User::class, 100)->create();
    	factory(Customer::class, 100)->create();
    	factory(Expense::class, 100)->create();
    	factory(Income::class, 100)->create();
    	factory(Refuel::class, 100)->create();
        factory(Trip::class, 100)->create();
    	factory(Contract::class, 100)->create();

        $intermediate_points = IntermediatePoint::all();
        $stops_and_services = StopAndService::all();

        Trip::all()->each(function ($trip) use ($intermediate_points, $stops_and_services) {
            $trip->intermediate_point()->attach(
                $intermediate_points->random(rand(0, 3))->pluck('id')->toArray()
            );

            $trip->stop_and_service()->attach(
                $stops_and_services->random(rand(0, 3))->pluck('id')->toArray()
            );
        });
    }
}
