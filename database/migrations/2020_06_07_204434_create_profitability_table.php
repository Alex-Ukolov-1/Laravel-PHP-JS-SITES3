<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfitabilityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profitability', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('name');
            $table->dateTime('date');
            $table->decimal('distance', 15, 2)->unsigned();
            $table->decimal('loading_volume', 15, 2)->unsigned();
            $table->integer('loading_volume_type_id')->unsigned();
            $table->decimal('loading_price', 15, 2)->unsigned();
            $table->integer('loading_price_type_id')->unsigned();
            $table->decimal('unloading_volume', 15, 2)->unsigned();
            $table->integer('unloading_volume_type_id')->unsigned();
            $table->decimal('unloading_price', 15, 2)->unsigned();
            $table->integer('unloading_price_type_id')->unsigned();
            $table->decimal('conversion_factor', 15, 2)->unsigned();
            $table->decimal('additional_overhead', 15, 2)->default('0.00')->unsigned();
            $table->integer('trip_direction_id')->unsigned();
            $table->decimal('price_of_fuel', 15, 2)->unsigned();
            $table->decimal('average_fuel_consumption', 15, 2)->unsigned();
            $table->decimal('fixed_overhead', 15, 2)->default('0.00')->unsigned();
            $table->decimal('driver_salary', 15, 2)->unsigned();
            $table->integer('driver_salary_type_id')->unsigned();
            $table->integer('driver_salary_direction_id')->unsigned();
            $table->boolean('with_taxes')->unsigned();
            $table->boolean('vat_in_income')->unsigned();
            $table->boolean('vat_in_fuel_expenses')->unsigned();
            $table->boolean('vat_in_cargo_expenses')->unsigned();
            $table->boolean('vat_in_fixed_overhead')->unsigned();
            $table->boolean('vat_in_additional_overhead')->unsigned();
            $table->text('comment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profitability');
    }
}
