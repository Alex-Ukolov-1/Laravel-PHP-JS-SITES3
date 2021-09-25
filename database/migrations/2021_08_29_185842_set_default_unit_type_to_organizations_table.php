<?php

use App\Models\Settings\UnitType;
use Illuminate\Database\Migrations\Migration;

class SetDefaultUnitTypeToOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        UnitType::where('name', '=', 'куб.м')->update(['default' => true]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        UnitType::where('name', '=', 'куб.м')->update(['default' => false]);
    }
}
