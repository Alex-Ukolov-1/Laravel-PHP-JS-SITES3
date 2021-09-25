<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangesToContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->boolean('customers_cargo')->unsigned()->nullable()->after('driver_salary_type_id');
            $table->decimal('loading_price', 15, 2)->unsigned()->nullable(true)->change();
            $table->integer('loading_payment_type_id')->unsigned()->nullable(true)->change();
            $table->integer('supplier_id')->unsigned()->nullable(true)->change();
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
            $table->decimal('loading_price', 15, 2)->unsigned()->nullable(false)->change();
            $table->integer('loading_payment_type_id')->unsigned()->nullable(false)->change();
            $table->integer('supplier_id')->unsigned()->nullable(false)->change();
            $table->dropColumn('customers_cargo');
        });
    }
}
