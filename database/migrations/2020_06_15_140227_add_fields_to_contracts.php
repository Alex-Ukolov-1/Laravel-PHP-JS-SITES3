<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToContracts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->integer('cargo_type_id')->unsigned();
            $table->integer('trip_direction_id')->unsigned();
            $table->decimal('distance', 15, 2)->unsigned();
            $table->decimal('conversion_factor', 15, 2)->unsigned();
            $table->decimal('driver_salary', 15, 2)->unsigned();
            $table->integer('driver_salary_type_id')->unsigned();
            $table->integer('departure_point_id')->unsigned();
            $table->integer('loading_unit_type_id')->unsigned();
            $table->decimal('loading_price', 15, 2)->unsigned();
            $table->integer('loading_payment_type_id')->unsigned();
            $table->integer('supplier_id')->unsigned();
            $table->integer('destination_id')->unsigned();
            $table->integer('unloading_unit_type_id')->unsigned();
            $table->decimal('unloading_price', 15, 2)->unsigned();
            $table->integer('unloading_payment_type_id')->unsigned();
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
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('cargo_type_id');
            $table->dropColumn('trip_direction_id');
            $table->dropColumn('distance');
            $table->dropColumn('conversion_factor');
            $table->dropColumn('driver_salary');
            $table->dropColumn('driver_salary_type_id');
            $table->dropColumn('departure_point_id');
            $table->dropColumn('loading_unit_type_id');
            $table->dropColumn('loading_price');
            $table->dropColumn('loading_payment_type_id');
            $table->dropColumn('supplier_id');
            $table->dropColumn('destination_id');
            $table->dropColumn('unloading_unit_type_id');
            $table->dropColumn('unloading_price');
            $table->dropColumn('unloading_payment_type_id');
            $table->dropColumn('vat_in_income');
            $table->dropColumn('vat_in_fuel_expenses');
            $table->dropColumn('vat_in_cargo_expenses');
            $table->dropColumn('vat_in_fixed_overhead');
            $table->dropColumn('vat_in_additional_overhead');
            $table->dropColumn('comment');
        });
    }
}
