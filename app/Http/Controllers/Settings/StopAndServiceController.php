<?php

namespace App\Http\Controllers\Settings;

use App\Models\Settings\StopAndService;
use App\Http\Controllers\Settings\SettingsController;

class StopAndServiceController extends SettingsController
{
    protected $model = StopAndService::class;

    protected $route = 'stops_and_services';

    protected $title = 'Стоянки и сервисы';
}
