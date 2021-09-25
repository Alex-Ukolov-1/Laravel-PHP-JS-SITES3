<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefuelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refuels', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('date', 0);
            $table->unsignedInteger('car_id');
            $table->unsignedInteger('user_id');
            $table->float('fuel');
            $table->decimal('money', 15, 2);
            $table->smallInteger('payment_type_id');
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
        Schema::dropIfExists('refuels');
    }
}
