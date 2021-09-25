<?php

namespace App\Http\Controllers\Settings;

use App\Models\Settings\CounterpartyType;
use App\Http\Controllers\Settings\SettingsController;

class CounterpartyTypeController extends SettingsController
{
    protected $model = CounterpartyType::class;

    protected $route = 'counterparty_types';

    protected $title = 'Типы контрагентов';
}
