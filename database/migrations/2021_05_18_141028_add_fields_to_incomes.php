<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToIncomes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->unsignedInteger('car_id')->nullable()->after('money');
            $table->unsignedInteger('customer_id')->nullable()->after('car_id');
            $table->unsignedInteger('cargo_type_id')->nullable()->after('customer_id');
            $table->unsignedInteger('cargo_unit_type_id')->nullable()->after('cargo_type_id');
            $table->decimal('cargo_amount', 15, 2)->nullable()->after('cargo_unit_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropColumn('car_id');
            $table->dropColumn('customer_id');
            $table->dropColumn('cargo_type_id');
            $table->dropColumn('cargo_unit_type_id');
            $table->dropColumn('cargo_amount');
        });
    }
}
