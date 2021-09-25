<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('date', 0);
            $table->unsignedInteger('car_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('cargo_type_id');
            $table->unsignedInteger('departure_point_id');
            $table->unsignedInteger('destination_id');
            $table->decimal('cargo_amount', 15, 2);
            $table->unsignedInteger('unit_type_id');
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
        Schema::dropIfExists('trips');
    }
}
