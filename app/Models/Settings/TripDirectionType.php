<?php

namespace App\Models\Settings;

use App\Models\Settings\SettingsModel;

class TripDirectionType extends SettingsModel
{
	static function getDefaultId() {
		$d = self::where('default', 1)->first();
		if ($d) return (int)$d->id;
		else return null;
	}
}
