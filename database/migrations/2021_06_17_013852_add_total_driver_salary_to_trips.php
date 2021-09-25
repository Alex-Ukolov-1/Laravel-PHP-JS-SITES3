<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Trip;

class AddTotalDriverSalaryToTrips extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->decimal('total_driver_salary', 15, 2)->nullable();
        });

        $trips = Trip::all();

        foreach ($trips as $trip) {
            $trip->update(['total_driver_salary' => $trip->calcDriverSalary()]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn('total_driver_salary');
        });
    }
}
