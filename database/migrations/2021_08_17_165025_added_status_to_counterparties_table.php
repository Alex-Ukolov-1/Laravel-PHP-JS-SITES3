<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddedStatusToCounterpartiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('counterparties', function (Blueprint $table) {
            $table->boolean('status')->default(1);
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->integer('driver_salary_type_id')->nullable()->change();
        });

        Schema::table('profitability', function (Blueprint $table) {
            $table->integer('driver_salary_type_id')->nullable()->change();
            $table->integer('driver_salary_direction_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('counterparties', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->integer('driver_salary_type_id')->nullable(false)->change();
        });

        Schema::table('profitability', function (Blueprint $table) {
            $table->integer('driver_salary_type_id')->nullable(false)->change();
            $table->integer('driver_salary_direction_id')->nullable(false)->change();
        });
    }
}
