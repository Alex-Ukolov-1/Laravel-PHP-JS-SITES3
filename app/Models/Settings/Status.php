<?php

namespace App\Models\Settings;

use App\Models\Settings\SettingsModel;

class Status extends SettingsModel
{
	protected $fillable = [
	  'name', 'type_id',
	];

	public function scopeContract($query) {
		return $query->where('type_id', 1);
	}
}
