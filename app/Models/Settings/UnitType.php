<?php

namespace App\Models\Settings;

use App\Models\Traits\FilterByRole;
use Illuminate\Database\Eloquent\Model;

class UnitType extends SettingsModel
{
	use FilterByRole;

    static function getDefaultId() {
        $d = self::where('default', 1)->first();
        if ($d) return (int)$d->id;
        else return null;
    }

    public static function getActualUnitFromProfit(string $unit_type_from_profit, int $organization_id){
        return UnitType::query()->where('organization_id', '=', $organization_id)->where('name', '=', $unit_type_from_profit)->pluck('id')->first();
    }
}
