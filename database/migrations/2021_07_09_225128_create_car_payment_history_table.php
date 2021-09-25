<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarPaymentHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_payment_history', function (Blueprint $table) {
            $table->id();
            $table->integer('car_id')->unsigned();
            $table->integer('payment_id')->unsigned()->nullable();
            $table->dateTime('created_at');
            $table->text('comment')->nullable();
            $table->date('paid_before');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('car_payment_history');
    }
}
