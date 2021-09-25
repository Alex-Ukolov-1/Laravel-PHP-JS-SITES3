<?php

namespace App\Http\Controllers\Settings;

use App\Models\Organization;
use App\Models\Settings\CargoType;
use App\Http\Controllers\Settings\SettingsController;
use Illuminate\Http\Request;

class CargoTypeController extends SettingsController
{
    protected $model = CargoType::class;

    protected $route = 'cargo_types';

    protected $title = 'Типы грузов';


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
            'admin_only' => true,
        ],
        [
            'name' => 'name',
            'data' => 'name',
            'title' => 'Название',
            'type' => 'string',
            'required' => true,
        ],
        [
            'name' => 'ratio_ton_cubic',
            'data' => 'ratio_ton_cubic',
            'title' => 'Коэффициент пересчета тонна/куб',
            'type' => 'float',
            'required' => true,
            'default_value' => 1.00
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
    ];

    public function list(Request $request, $main_field_name = 'name') {
        return \response()->json(
            $this->model->select(['id', 'name as text', 'ratio_ton_cubic'])
                ->where('name', 'like', '%'.$request->input('q').'%')
                ->applyScopes()->getQuery()->get()
        );
    }
}
