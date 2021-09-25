<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYooPaymentDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yoo_payment_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('organization_id');
            $table->boolean('auto_renew')->default(0);
            $table->string('yandex_payment_method_id', 255)->nullable();
            $table->string('card_last4', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_data');
    }
}
