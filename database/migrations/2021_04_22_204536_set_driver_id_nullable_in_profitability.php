<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetDriverIdNullableInProfitability extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profitability', function (Blueprint $table) {
            $table->unsignedInteger('driver_id')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profitability', function (Blueprint $table) {
            $table->unsignedInteger('driver_id')->nullable(false)->change();
        });
    }
}
