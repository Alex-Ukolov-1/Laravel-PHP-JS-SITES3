<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToncubratioToCargoTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cargo_types', function (Blueprint $table) {
            $table->float('ratio_ton_cubic')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cargo_types', function (Blueprint $table) {
            $table->dropColumn('ratio_ton_cubic');
        });
    }
}
