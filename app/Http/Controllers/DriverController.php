<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\DriversHistory;
use App\Models\Organization;
use App\Models\Driver;
use App\Models\Car;
use App\Repositories\StatusRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Auth;

class DriverController extends CRUDController
{
    protected $model = Driver::class;

    protected $route = 'drivers';

    protected $title = 'Водители';

    protected $row_buttons = ['user_login', 'edit', 'delete'];

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
            'title' => 'Имя',
            'type' => 'text',
            'required' => true,
            'strict_search' => '*',
        ],
        [
            'name' => 'car_id',
            'data' => 'car->number',
            'title' => 'Машина',
            'type' => 'select',
            'source' => Car::class,
        ],
        [
            'name' => 'contract_id',
            'data' => 'contract->name_for_list',
            'title' => 'Заказ',
            'type' => 'select',
            'source' => Contract::class,
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
            'name' => 'phone',
            'data' => 'phone',
            'title' => 'Телефон',
            'type' => 'text',
        ],
        [
            'name' => 'balance',
            'data' => 'balance',
            'title' => 'Баланс',
            'type' => 'float',
            'value_append' => ' руб.',
            'skipInCreate' => true,
            'skipInEdit' => true,
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
            'name' => 'taxes_in_salary',
            'data' => 'taxes_in_salary',
            'title' => 'Учесть в з/п водителя социальные налоги и НДФЛ',
            'type' => 'boolean',
            'skipInTable' => true,
        ]
    ];

    public $auto_load_relations = ['organization', 'car', 'contract'];

    public function getCar(Request $request)
    {
        $userId = $request->input('userId') ?? null;
        $carId = Driver::find($userId)->car_id;

        if ($carId) {
            return response()->json(['carId' => $carId, 'status' => 1]);
        }

        return response()->json(['status' => 0]);
    }

    public function getContract(Request $request)
    {
        $userId = $request->input('userId') ?? null;
        $contractId = Driver::find($userId)->contract_id;

        if ($contractId) {
            return response()->json(['contractId' => $contractId, 'status' => 1]);
        }

        return response()->json(['status' => 0]);
    }

    public function update(Request $request, $id)
    {
        $this->editValidate['email'] = ['required', 'string', 'email', 'max:255', 'unique:admins', 'unique:organizations', Rule::unique('drivers')->ignore($id)];

        return parent::update($request, $id);
    }

    public function edit($id, $additional_data = [])
    {
        return parent::edit($id, [
            'drivers_history_records' => DriversHistory::where('driver_id', $id)->orderBy('id', 'DESC')->with(['car'])->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->createValidate['email'] = ['required', 'string', 'email', 'max:255', 'unique:admins', 'unique:organizations', 'unique:drivers'];

        return parent::store($request);
    }
}
