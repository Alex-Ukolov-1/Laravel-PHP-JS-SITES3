<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToExpenses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->unsignedInteger('supplier_id')->nullable()->after('money');
            $table->unsignedInteger('cargo_type_id')->nullable()->after('supplier_id');
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
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('supplier_id');
            $table->dropColumn('cargo_type_id');
            $table->dropColumn('cargo_unit_type_id');
            $table->dropColumn('cargo_amount');
        });
    }
}
