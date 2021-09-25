<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('number', 35)->unique();
            $table->string('code', 10)->unique();
            $table->string('name');
            $table->date('date');
            $table->decimal('budget', 15, 2)->default('0.00')->nullable();
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('status_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contracts');
    }
}
