<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteVatInFixedToContracts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('vat_in_fixed_overhead');
            $table->dropColumn('vat_in_additional_overhead');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->boolean('vat_in_fixed_overhead')->unsigned();
            $table->boolean('vat_in_additional_overhead')->unsigned();
        });
    }
}
