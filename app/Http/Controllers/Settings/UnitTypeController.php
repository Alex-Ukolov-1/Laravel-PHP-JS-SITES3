<?php

namespace App\Http\Controllers\Settings;

use App\Models\Organization;
use App\Models\Settings\UnitType;
use App\Http\Controllers\Settings\SettingsController;

class UnitTypeController extends SettingsController
{
    protected $model = UnitType::class;

    protected $route = 'unit_types';

    protected $title = 'Единицы измерения';

    protected $fields = [
        [
            'name' => 'id',
            'data' => 'id',
            'title' => 'ID',
            'type' => 'number',
            'skipInTable' => true,
            'skipInCreate' => true,
            'skipInEdit' => true,
        ],
        [
            'name' => 'organization_id',
            'data' => 'organization->name',
            'title' => 'Организация',
            'type' => 'select',
            'source' => Organization::class,
            'required' => true,
            'forAdminOnly' => true,
        ],
        [
            'name' => 'name',
            'data' => 'name',
            'title' => 'Название',
            'type' => 'string',
            'required' => true,
        ],
        [
            'name' => 'status',
            'data' => 'status',
            'title' => 'Статус',
            'type' => 'boolean',
            'boolean_turn_on' => 'Включен',
            'boolean_turn_off' => 'Отключен',
            'required' => true,
            'default_value' => 1,
        ],
        [
            'name' => 'default',
            'title' => 'По-умолчанию',
            'data' => 'default',
            'type' => 'boolean'
        ]
    ];


}
