<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterNullablesContracts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contracts', function(Blueprint $table){
            $table->integer('cargo_type_id')->nullable()->change();
            $table->integer('departure_point_id')->nullable()->change();
            $table->integer('destination_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contracts', function(Blueprint $table){
            $table->integer('cargo_type_id')->nullable(false)->change();
            $table->integer('departure_point_id')->nullable(false)->change();
            $table->integer('destination_id')->nullable(false)->change();
        });
    }
}
