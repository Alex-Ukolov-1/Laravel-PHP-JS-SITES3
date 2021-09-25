<?php

namespace App\Http\Controllers;

use App\Models\Contractor;
use App\Models\Organization;
use Illuminate\Http\Request;

class ContractorController extends CRUDController
{
    protected $model = Contractor::class;

    protected $route = 'contractors';

    protected $title = 'Исполнители';

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
            'type' => 'text',
            'required' => true,
        ],
        [
            'name' => 'email',
            'data' => 'email',
            'title' => 'E-Mail',
            'type' => 'text',
        ],
        [
            'name' => 'phone',
            'data' => 'phone',
            'title' => 'Телефон',
            'type' => 'text',
        ],
        [
            'name' => 'inn',
            'data' => 'inn',
            'title' => 'ИНН',
            'type' => 'text',
        ],
        [
            'name' => 'bik',
            'data' => 'bik',
            'title' => 'БИК',
            'type' => 'text',
        ],
        [
            'name' => 'checking_account',
            'data' => 'checking_account',
            'title' => 'Расчётный счёт',
            'type' => 'text',
        ],
        [
            'name' => 'note',
            'data' => 'note',
            'title' => 'Примечание',
            'type' => 'textarea',
        ],
    ];

    public function list(Request $request, $main_field_name = 'name') {
        return \response()->json(
            $this->model->select(['id', "$main_field_name as text"])
                ->where($main_field_name, 'like', '%'.$request->input('q').'%')
                ->where('status', '=', 1)
                ->applyScopes()->getQuery()->get()
        );
    }
}
