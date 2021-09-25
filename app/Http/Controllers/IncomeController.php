<?php

namespace App\Http\Controllers;

use App\Models\Settings\PaymentType;
use Illuminate\Http\Request;
use App\Models\Income;
use App\Models\Driver;
use App\Models\Car;
use App\Models\Customer;
use App\Models\Organization;
use App\Models\Settings\CargoType;
use App\Models\Settings\UnitType;
use Auth;
use App\Http\Controllers\Settings\CargoTypeController;
use App\Http\Controllers\Settings\UnitTypeController;

class IncomeController extends CRUDController
{
    protected $model = Income::class;

    protected $route = 'incomes';

    protected $title = 'Доходы';

    protected $sort = self::SORT_BY_DATE;

    protected $datatable_plugins = ['sum_of_money_and_cargo'];

    protected $fields = [
        [
            'name'         => 'id',
            'data'         => 'id',
            'title'        => 'ID',
            'type'         => 'number',
            'skipInTable'  => true,
            'skipInCreate' => true,
            'skipInEdit'   => true,
        ],
        [
            'name'         => 'organization_id',
            'data'         => 'organization->name',
            'title'        => 'Организация',
            'type'         => 'select',
            'source'       => Organization::class,
            'required'     => true,
            'forAdminOnly' => true,
        ],
        [
            'name'     => 'date',
            'data'     => 'date',
            'title'    => 'Дата',
            'type'     => 'date',
            'required' => true,
        ],
        [
            'name'     => 'money',
            'data'     => 'money',
            'title'    => 'Сумма',
            'type'     => 'float',
            'required' => true,
        ],
        [
            'name'         => 'driver_id',
            'data'         => 'driver->name',
            'title'        => 'Получатель',
            'type'         => 'select2',
            'source'       => Driver::class,
            'source_route' => 'drivers',
            'controller'   => DriverController::class,
            'select2_enable_create' => true
        ],
        [
            'name'         => 'car_id',
            'data'         => 'car->number',
            'title'        => 'Машина',
            'type'         => 'select2',
            'source'       => Car::class,
            'source_route' => 'cars',
            'controller'   => CarController::class,
        ],
        [
            'name'         => 'customer_id',
            'data'         => 'customer->name',
            'title'        => 'Заказчик',
            'type'         => 'select2',
            'source'       => Customer::class,
            'source_route' => 'customers',
            'controller'   => CustomerController::class,
            'select2_enable_create' => true
        ],
        [
            'name'         => 'cargo_type_id',
            'data'         => 'cargo_type->name',
            'title'        => 'Груз',
            'type'         => 'select2',
            'source'       => CargoType::class,
            'source_route' => 'cargo_types',
            'controller'   => CargoTypeController::class,
            'select2_enable_create' => true
        ],
        [
            'name'         => 'cargo_unit_type_id',
            'data'         => 'cargo_unit_type->name',
            'title'        => 'Ед.измерения груза',
            'type'         => 'select2',
            'source'       => UnitType::class,
            'source_route' => 'unit_types',
            'controller'   => UnitTypeController::class,
            'select2_enable_create' => true,
        ],
        [
            'name'  => 'cargo_amount',
            'data'  => 'cargo_amount',
            'title' => 'Кол.груза',
            'type'  => 'float',
        ],
        [
            'name'        => 'comment',
            'data'        => 'comment',
            'title'       => 'Примечание',
            'type'        => 'textarea',
            'skipInTable' => true,
        ],
        [
            'name'   => 'payment_type_id',
            'data'   => 'payment_type->name',
            'title'  => 'Форма оплаты',
            'type'   => 'select',
            'source' => PaymentType::class,
            'required' => true
        ]
    ];

    protected $driver_fields = [
        [
            'name'     => 'date',
            'data'     => 'date',
            'title'    => 'Дата',
            'type'     => 'date',
            'skipInCreate' => true,
            'skipInEdit' => true,
        ],
        [
            'name'     => 'money',
            'data'     => 'money',
            'title'    => 'Сумма',
            'type'     => 'float',
            'required' => true,
        ],
        [
            'name'         => 'car_id',
            'data'         => 'car->number',
            'title'        => 'Машина',
            'type'         => 'select2',
            'source'       => Car::class,
            'source_route' => 'cars',
            'controller'   => CarController::class,
            'skipInCreate' => true,
            'skipInEdit'   => true,
        ],
        [
            'name'         => 'customer_id',
            'data'         => 'customer->name',
            'title'        => 'Заказчик',
            'type'         => 'select2',
            'source'       => Customer::class,
            'source_route' => 'customers',
            'controller'   => CustomerController::class,
            'select2_enable_create' => true,
            'skipInCreate' => true,
            'skipInEdit'   => true,
        ],
        [
            'name'         => 'cargo_type_id',
            'data'         => 'cargo_type->name',
            'title'        => 'Груз',
            'type'         => 'select2',
            'source'       => CargoType::class,
            'source_route' => 'cargo_types',
            'controller'   => CargoTypeController::class,
            'select2_enable_create' => true,
            'skipInCreate' => true,
            'skipInEdit'   => true,
        ],
        [
            'name'         => 'cargo_unit_type_id',
            'data'         => 'cargo_unit_type->name',
            'title'        => 'Ед.измерения груза',
            'type'         => 'select2',
            'source'       => UnitType::class,
            'source_route' => 'unit_types',
            'controller'   => UnitTypeController::class,
            'select2_enable_create' => true,
            'skipInCreate' => true,
            'skipInEdit'   => true,
        ],
        [
            'name'  => 'cargo_amount',
            'data'  => 'cargo_amount',
            'title' => 'Кол.груза',
            'type'  => 'float',
            'skipInCreate' => true,
            'skipInEdit'   => true,
        ],
        [
            'name'        => 'comment',
            'data'        => 'comment',
            'title'       => 'Примечание',
            'type'        => 'textarea',
            'skipInTable' => true,
            'required'    => true,
        ],
        [
            'name'   => 'payment_type_id',
            'data'   => 'payment_type->name',
            'title'  => 'Форма оплаты',
            'type'   => 'select',
            'source' => PaymentType::class,
            'skipInCreate' => true,
            'skipInEdit'   => true,
        ]
    ];

    public $auto_load_relations = ['organization', 'driver', 'car', 'customer', 'cargo_type', 'cargo_unit_type', 'payment_type'];

    public function __construct() {
        $this->middleware(function ($request, $next) {
            if (Auth::guard('driver')->check()) {
                $this->top_buttons = ['add', 'select_all', 'deselect_all'];
                $this->fields = $this->driver_fields;
            }

            return $next($request);
        });

        parent::__construct();
    }

    public function store(Request $request) {

        if (Auth::guard('driver')->check()) {
            $driver = Auth::guard('driver')->user();
            $contract = $driver->contract;

            $this->request_data['date'] = date('Y-m-d');
            $this->request_data['car_id'] = $driver->car_id;

            if ($contract) {
                $this->request_data['customer_id'] = $contract->customer_id;
                $this->request_data['cargo_type_id'] = $contract->cargo_type_id;
                $this->request_data['cargo_unit_type_id'] = $contract->loading_unit_type_id;
            }
        }

        return parent::store($request);
    }

    public function getFiltered(Request $request) {
        $filtered_data = parent::getFiltered($request);

        $sum_of_money = $this->filtered_query_without_pagination->sum('money');
        $sum_of_cargo = $this->filtered_query_without_pagination->sum('cargo_amount');

        return array_merge($filtered_data, [
            'sum_of_money' => $sum_of_money,
            'sum_of_cargo' => $sum_of_cargo,
        ]);
    }

}
