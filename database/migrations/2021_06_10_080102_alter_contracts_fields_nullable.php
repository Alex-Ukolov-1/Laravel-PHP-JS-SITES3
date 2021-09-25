<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterContractsFieldsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->integer('customer_id')->nullable()->change();
            $table->integer('contractor_id')->nullable()->change();
            $table->integer('supplier_id')->nullable()->change();
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
            $table->integer('customer_id')->nullable(false)->change();
            $table->integer('contractor_id')->nullable(false)->change();
            $table->integer('supplier_id')->nullable(false)->change();
        });
    }
}
