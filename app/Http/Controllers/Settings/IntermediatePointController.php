<?php

namespace App\Http\Controllers\Settings;

use App\Models\Settings\IntermediatePoint;
use App\Http\Controllers\Settings\SettingsController;

class IntermediatePointController extends SettingsController
{
    protected $model = IntermediatePoint::class;

    protected $route = 'intermediate_points';

    protected $title = 'Пункты следования';
}
