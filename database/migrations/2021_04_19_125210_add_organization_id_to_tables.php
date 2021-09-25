<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrganizationIdToTables extends Migration
{
    private $tables = [
        'profitability', 'incomes', 'expenses', 'refuels', 'counterparties', 'contracts', 'expense_categories', 'departure_points', 'destinations', 
        'intermediate_points', 'stops_and_services', 'payment_types', 'cargo_types'
    ];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->tables as $table_name) {
            Schema::table($table_name, function (Blueprint $table) {
                $table->unsignedInteger('organization_id')->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->tables as $table_name) {
            Schema::table($table_name, function (Blueprint $table) {
                $table->dropColumn('organization_id');
            });
        }
    }
}
