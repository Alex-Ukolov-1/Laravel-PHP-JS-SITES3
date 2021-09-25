<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FillOrganizationIdInTables extends Migration
{
    private $tables = [
        'cars', 'drivers', 'trips', 'profitability', 'incomes', 'expenses', 'refuels', 'counterparties', 'contracts', 
        'expense_categories', 'departure_points', 'destinations', 'intermediate_points', 'stops_and_services', 'payment_types', 
        'cargo_types'
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->tables as $table_name) {
            DB::table($table_name)->update(['organization_id' => 1]);
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
            DB::table($table_name)->update(['organization_id' => 0]);
        }
    }
}
