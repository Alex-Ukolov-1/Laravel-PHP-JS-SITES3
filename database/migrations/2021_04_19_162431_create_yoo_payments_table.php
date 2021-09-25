<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYooPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yoo_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('organization_id');
            $table->string('payment_id', 255)->nullable();
            $table->string('card_last4', 10)->nullable();
            $table->string('type', 100)->default('payment');
            $table->smallInteger('months')->unsigned();
            $table->decimal('amount', 15, 2)->unsigned();
            $table->string('status', 50)->nullable();
            $table->string('cars', 1024)->nullable();
            $table->text('detail')->nullable();
            $table->string('cancellation_party', 255)->nullable();
            $table->string('cancellation_reason', 255)->nullable();
            $table->text('comment')->nullable();
            $table->boolean('created_manually')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
