<?php

namespace App\Http\Controllers\Settings;

use App\Models\Settings\Destination;
use App\Http\Controllers\Settings\SettingsController;

class DestinationController extends SettingsController
{
    protected $model = Destination::class;

    protected $route = 'destinations';

    protected $title = 'Пункты назначения';
}
