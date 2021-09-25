<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameUserIdToDriverId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('refuels', function (Blueprint $table) {
            $table->renameColumn('user_id', 'driver_id');
        });

        Schema::table('profitability', function (Blueprint $table) {
            $table->renameColumn('user_id', 'driver_id');
        });

        Schema::table('drivers_history', function (Blueprint $table) {
            $table->renameColumn('user_id', 'driver_id');
        });

        Schema::table('incomes', function (Blueprint $table) {
            $table->renameColumn('user_id', 'driver_id');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->renameColumn('user_id', 'driver_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('refuels', function (Blueprint $table) {
            $table->renameColumn('driver_id', 'user_id');
        });

        Schema::table('profitability', function (Blueprint $table) {
            $table->renameColumn('driver_id', 'user_id');
        });

        Schema::table('drivers_history', function (Blueprint $table) {
            $table->renameColumn('driver_id', 'user_id');
        });

        Schema::table('incomes', function (Blueprint $table) {
            $table->renameColumn('driver_id', 'user_id');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->renameColumn('driver_id', 'user_id');
        });
    }
}
