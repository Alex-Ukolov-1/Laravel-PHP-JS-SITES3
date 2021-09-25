<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Repositories\StatusRepository;
use Illuminate\Http\Request;

class OrganizationController extends CRUDController
{
    protected $model = Organization::class;

    protected $route = 'organizations';

    protected $title = 'Организации';

    protected $row_buttons = ['organization_login', 'edit', 'delete'];

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
            'name' => 'name',
            'data' => 'name',
            'title' => 'Название',
            'type' => 'text',
            'required' => true,
            'strict_search' => '*',
        ],
        [
            'name' => 'phone',
            'data' => 'phone',
            'title' => 'Телефон',
            'type' => 'text',
        ],
        [
            'name' => 'email',
            'data' => 'email',
            'title' => 'E-Mail',
            'type' => 'email',
            'required' => true,
        ],
        [
            'name' => 'password',
            'data' => 'password',
            'title' => 'Пароль',
            'type' => 'password',
            'skipInTable' => true,
            'skipInEdit' => true,
            'skipInShow' => true,
            'required' => true,
        ],
        [
            'name' => 'new_password',
            'data' => 'new_password',
            'title' => 'Новый пароль',
            'type' => 'password',
            'skipInTable' => true,
            'skipInCreate' => true,
            'skipInShow' => true,
        ],
        [
            'name' => 'status',
            'data' => 'status_name',
            'title' => 'Статус',
            'type' => 'select',
            'source' => StatusRepository::class,
            'required' => true,
        ],
        [
            'name' => 'price_per_km',
            'data' => 'price_per_km',
            'title' => 'Цена за 1 км при расчёте З/П водителя',
            'type' => 'text',
            'skipInTable' => true,
            'required' => true,
        ],
        [
            'name' => 'vat_in_fuel_expenses',
            'data' => 'vat_in_fuel_expenses',
            'title' => 'Учет НДС - В расходах на топливо',
            'type' => 'boolean',
            'skipInTable' => true,
            'required' => true,
        ],
        [
            'name' => 'fuel_price',
            'data' => 'fuel_price',
            'title' => 'Цена топлива, руб./л ',
            'type' => 'text',
            'skipInTable' => true,
            'required' => true,
        ],
        [
            'name' => 'average_fuel_consumption',
            'data' => 'average_fuel_consumption',
            'title' => 'Средний расход топлива ТС, л/100 км',
            'type' => 'text',
            'skipInTable' => true,
            'required' => true,
        ],
    ];

    public $auto_load_relations = [];

    public function store(Request $request)
    {
        $this->createValidate = [
            'phone' => ['max:18', 'regex:/^(\+7)?[\s\-]?\(?[489][0-9]{2}\)?[\s\-]?[0-9]{3}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:organizations'],
        ];

        return parent::store($request);
    }

    public function edit($id, $additional_data = [])
    {
        if((auth('organization')->id() != $id) && !auth('admin')->check() ){
            return redirect()->route('trips.index');
        }
        $item = $this->model->findOrFail($id);
        return view('crud.create_edit', array_merge([
            'fields' => $this->editFields,
            'item' => $item,
            'title' => $this->title.'. Редактирование',
            'route' => $this->route,
            'form_buttons' => $this->form_buttons,
            'type' => 'edit',
            'append_js' => $this->append_js,
        ], $additional_data));
    }

    public function update(Request $request, $id)
    {
        $this->editValidate = [
            'phone' => ['max:18', 'regex:/^(\+7)?[\s\-]?\(?[489][0-9]{2}\)?[\s\-]?[0-9]{3}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:organizations,email,' . $id],
        ];

        return parent::update($request, $id);
    }

    public function getFiltered(Request $request)
    {
        //список организаций для админа - полный, для самой организации - только ее
        if(!auth('admin')->check()) {
            $request->merge(['id' => auth('organization')->id()]);
        }
        return parent::getFiltered($request);
    }

}
