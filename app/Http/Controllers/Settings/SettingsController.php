<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\CRUDController;
use App\Models\Organization;
use Illuminate\Http\Request;

class SettingsController extends CRUDController
{
    protected $model;

    protected $route;

    protected $title;

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
    ];

    public $auto_load_relations = ['organization'];

    public function list(Request $request, $main_field_name = 'name') {
        return \response()->json(
            $this->model->select(['id', "$main_field_name as text"])
                ->where($main_field_name, 'like', '%'.$request->input('q').'%')
                ->where('status', '=', 1)
                ->applyScopes()->getQuery()->get()
        );
    }
}
