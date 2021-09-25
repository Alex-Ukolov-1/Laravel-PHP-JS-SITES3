<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Organization;
use App\Models\Settings\UnitType;

class AddUnitTypesToOrganizations extends Migration
{
    private $values = ['куб.м', 'тонна', 'машина', 'усл.ед'];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        UnitType::create(['organization_id' => 1, 'name' => 'усл.ед']);

        $organization_ids = Organization::where('id', '!=', 1)->pluck('id');

        foreach ($organization_ids as $organization_id) {
            foreach ($this->values as $name) {
                UnitType::create(['organization_id' => $organization_id, 'name' => $name]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        UnitType::where('organization_id', '!=', 1)->forceDelete();
        UnitType::where('organization_id', 1)->where('name', 'усл.ед')->forceDelete();
    }
}
