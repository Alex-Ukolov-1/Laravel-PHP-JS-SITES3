<?php

namespace App\Models\Settings;

use App\Models\Settings\SettingsModel;
use App\Models\Traits\FilterByRole;

class StopAndService extends SettingsModel
{
	use FilterByRole;

	protected $table = 'stops_and_services';
}
