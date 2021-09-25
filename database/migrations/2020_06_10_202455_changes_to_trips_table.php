<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangesToTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->unsignedInteger('contract_id')->nullable();
            $table->renameColumn('cargo_amount', 'loading_cargo_amount');
            $table->decimal('unloading_cargo_amount', 15, 2)->nullable();
            $table->renameColumn('unit_type_id', 'loading_unit_type_id');
            $table->unsignedInteger('unloading_unit_type_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn('contract_id');
            $table->renameColumn('loading_cargo_amount', 'cargo_amount');
            $table->dropColumn('unloading_cargo_amount');
            $table->renameColumn('loading_unit_type_id', 'unit_type_id');
            $table->dropColumn('unloading_unit_type_id');
        });
    }
}
