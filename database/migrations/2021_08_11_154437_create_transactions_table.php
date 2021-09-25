<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('driver_id');
            $table->unsignedInteger('organization_id');
            $table->tinyInteger('type');
            $table->unsignedInteger('action_id')->nullable();
            $table->text('description')->nullable();
            $table->decimal('action_balance', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);
            $table->date('date');
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::table('drivers', function (Blueprint $table) {
            $table->decimal('balance', 15, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn('balance');
        });

        Schema::dropIfExists('transactions');
    }
}
