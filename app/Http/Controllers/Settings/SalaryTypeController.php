<?php

namespace App\Http\Controllers\Settings;

use App\Models\Settings\SalaryType;
use App\Http\Controllers\Settings\SettingsController;

class SalaryTypeController extends SettingsController
{
    protected $model = SalaryType::class;

    protected $route = 'salary_types';

    protected $title = 'Тип оплаты водителю';
}
