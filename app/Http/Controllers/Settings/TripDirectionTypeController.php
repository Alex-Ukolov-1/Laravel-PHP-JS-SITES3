<?php

namespace App\Http\Controllers\Settings;

use App\Models\Settings\TripDirectionType;
use App\Http\Controllers\Settings\SettingsController;

class TripDirectionTypeController extends SettingsController
{
    protected $model = TripDirectionType::class;

    protected $route = 'trip_direction_types';

    protected $title = 'Типы рейсов';

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
            'name' => 'default',
            'data' => 'default',
            'title' => 'Выбрано по-умолчанию',
            'type' => 'boolean',
            'required' => false,
        ],
    ];
}
