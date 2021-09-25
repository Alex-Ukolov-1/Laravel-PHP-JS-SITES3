<?php

namespace App\Http\Controllers\Settings;

use App\Models\Settings\DeparturePoint;
use App\Http\Controllers\Settings\SettingsController;

class DeparturePointController extends SettingsController
{
    protected $model = DeparturePoint::class;

    protected $route = 'departure_points';

    protected $title = 'Пункты отправления';
}
