<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Profitability;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Contractor;
use App\Models\Contract;
use App\Models\Settings\Status;
use App\Models\Settings\PaymentType;
use App\Models\Settings\SalaryType;
use App\Models\Settings\DeparturePoint;
use App\Models\Settings\CargoType;
use App\Models\Settings\TripDirectionType;
use App\Models\Settings\UnitType;
use App\Models\Settings\Destination;
use App\Models\Organization;
use App\Http\Controllers\Settings\CargoTypeController;
use App\Http\Controllers\Settings\DeparturePointController;
use App\Http\Controllers\Settings\DestinationController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ContractorController;
use Illuminate\Http\Request;
use Auth;
use DB;

class ContractController extends CRUDController
{
    protected $model = Contract::class;

    protected $route = 'contracts';

    protected $title = 'Заказы';

    protected $sort = self::SORT_BY_DATE;

    protected $form_buttons = ['cancel', 'save', 'save_and_exit'];
    protected $row_buttons = ['trips_by_contract', 'edit', 'delete'];

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
            'onchange' => "selectOrganization(this, 'organizations')",
            'required' => true,
            'forAdminOnly' => true,
            'skipInTable' => true,
            'skipForDriver' => true,
        ],

        [
            'name' => 'number',
            'data' => 'number',
            'title' => '№ заказа',
            'type' => 'text',
            'required' => true,
            'strict_search' => true,
            'skipForDriver' => true,
        ],

        [
            'name' => 'date',
            'data' => 'date',
            'title' => 'Дата',
            'type' => 'date',
            'required' => true,
            'skipInTable' => true,
            'skipForDriver' => true,
        ],

        [
            'name' => 'cargo_type_id',
            'data' => 'cargo_type->name',
            'title' => 'Груз',
            'type' => 'select2',
            'source' => CargoType::class,
            'source_route' => 'cargo_types',
            'controller' => CargoTypeController::class,
            'required' => true,
            'class' => 'cargo-type-select',
            'skipInTable' => true,
            'select2_enable_create' => true
        ],

        [
            'name' => 'customers_cargo',
            'data' => 'customers_cargo',
            'title' => 'Только перевозка (груз заказчика)',
            'type' => 'boolean:checkbox',
            'onchange' => "customersCargo(this, ['loading_price', 'loading_payment_type_id', 'supplier_id'])",
            'skipInTable' => true,
            'skipForDriver' => true,
        ],

        [
            'name' => 'distance',
            'data' => 'distance',
            'title' => 'Расстояние рейса, км',
            'type' => 'float',
            'required' => true,
        ],

        [
            'title' => 'Погрузка',
            'type' => 'subtitle',
            'skipInTable' => true,
        ],

        [
            'name' => 'departure_point_id',
            'data' => 'departure_point->name',
            'title' => 'Пункт погрузки',
            'type' => 'select2',
            'source' => DeparturePoint::class,
            'source_route' => 'departure_points',
            'controller' => DeparturePointController::class,
            'required' => true,
            'class' => 'departure_point',
            'skipInTable' => true,
            'select2_enable_create' => true
        ],

        [
            'name' => 'loading_unit_type_id',
            'data' => 'loading_unit_type->name',
            'title' => 'Ед.измерения груза',
            'type' => 'select',
            'source' => UnitType::class,
            'required' => true,
            'skipInTable' => true,
            'skipForDriver' => true,
        ],

        [
            'name' => 'loading_price',
            'data' => 'loading_price',
            'title' => 'Цена за ед.груза, Р',
            'type' => 'float',
            'required' => true,
            'skipInTable' => true,
            'skipForDriver' => true,
        ],

        [
            'name' => 'loading_payment_type_id',
            'data' => 'loading_payment_type->name',
            'title' => 'Форма оплаты',
            'type' => 'select',
            'source' => PaymentType::class,
            'required' => true,
            'skipInTable' => true,
            'skipForDriver' => true,
        ],

        [
            'name' => 'supplier_id',
            'data' => 'supplier->name',
            'title' => 'Поставщик',
            'type' => 'select2',
            'source' => Supplier::class,
            'source_route' => 'suppliers',
            'controller' => SupplierController::class,
            'required' => true,
            'class' => 'supplier-select',
            'select2_enable_create' => true,
            'skipForDriver' => true,
        ],

        [
            'title' => 'Разгрузка',
            'type' => 'subtitle',
            'skipInTable' => true,
        ],

        [
            'name' => 'destination_id',
            'data' => 'destination->name',
            'title' => 'Пункт разгрузки',
            'type' => 'select2',
            'source' => Destination::class,
            'source_route' => 'destinations',
            'controller' => DestinationController::class,
            'required' => true,
            'class' => 'destination-select',
            'skipInTable' => true,
            'select2_enable_create' => true
        ],

        [
            'name' => 'unloading_unit_type_id',
            'data' => 'unloading_unit_type->name',
            'title' => 'Ед.измерения груза',
            'type' => 'select',
            'source' => UnitType::class,
            'required' => true,
            'skipInTable' => true,
            'skipForDriver' => true,
        ],

        [
            'name' => 'unloading_price',
            'data' => 'unloading_price',
            'title' => 'Цена за ед.груза, Р',
            'type' => 'float',
            'required' => true,
            'skipForDriver' => true,
        ],

        [
            'name' => 'unloading_payment_type_id',
            'data' => 'unloading_payment_type->name',
            'title' => 'Форма оплаты',
            'type' => 'select',
            'source' => PaymentType::class,
            'required' => true,
            'skipForDriver' => true,
        ],

        [
            'name' => 'customer_id',
            'data' => 'customer->name',
            'title' => 'Заказчик',
            'type' => 'select2',
            'source' => Customer::class,
            'source_route' => 'customers',
            'controller' => CustomerController::class,
            'required' => true,
            'class' => 'customer-select',
            'select2_enable_create' => true,
            'skipForDriver' => true,
        ],

        [
            'title' => 'Учёт НДС',
            'type' => 'subtitle',
            'skipInTable' => true,
            'skipForDriver' => true,
        ],

        [
            'name' => 'vat_in_income',
            'data' => 'vat_in_income',
            'title' => 'В доходах',
            'type' => 'boolean',
            'required' => true,
            'default_value' => 0,
            'skipInTable' => true,
            'skipForDriver' => true,
        ],

        [
            'name' => 'vat_in_fuel_expenses',
            'data' => 'vat_in_fuel_expenses',
            'title' => 'В расходах на топливо',
            'type' => 'boolean',
            'required' => true,
            'default_value' => 0,
            'skipInCreate' => true,
            'skipInEdit' => true,
            'skipInTable' => true,
            'skipForDriver' => true,
        ],

        [
            'name' => 'vat_in_cargo_expenses',
            'data' => 'vat_in_cargo_expenses',
            'title' => 'В расходах на груз',
            'type' => 'boolean',
            'required' => true,
            'default_value' => 0,
            'skipInTable' => true,
            'skipForDriver' => true,
        ],

        [
            'title' => '',
            'type' => 'subtitle',
            'skipInTable' => true,
        ],

        [
            'name' => 'contractor_id',
            'data' => 'contractor->name',
            'title' => 'Исполнитель',
            'type' => 'select2',
            'source' => Contractor::class,
            'source_route' => 'contractors',
            'controller' => ContractorController::class,
            'required' => true,
            'class' => 'contractor-select',
            'select2_enable_create' => true,
            'skipForDriver' => true,
        ],

        [
            'name' => 'status_id',
            'data' => 'status->name',
            'title' => 'Статус',
            'type' => 'select',
            'source' => Status::class,
            'scope' => 'contract',
            'required' => true,
            'default_value' => 2,
            'skipForDriver' => true,
        ],

        [
            'name' => 'name',
            'data' => 'name',
            'title' => 'Название',
            'type' => 'text',
            'required' => true,
            'skipForDriver' => true,
        ],

        [
            'name' => 'comment',
            'data' => 'comment',
            'title' => 'Примечания',
            'type' => 'textarea',
            'required' => false,
            'skipInTable' => true,
            'skipForDriver' => true,
        ],

        [
            'name' => 'trip_direction_id',
            'data' => 'trip_direction_id',
            'title' => 'Понятие "РЕЙС"',
            'type' => 'select',
            'source' => TripDirectionType::class,
            'required' => true,
            'skipInTable' => true,
            'skipForDriver' => true,
        ],

        [
            'name' => 'distance_price',
            'data' => 'distance_price',
            'title' => 'Цена за 1 км при расчёте З/П водителя',
            'type' => 'float',
            'required' => true,
            'skipInTable'  => true,
            'skipInCreate' => true,
            'skipForDriver' => true,
        ],

        [
            'name' => 'conversion_factor',
            'data' => 'conversion_factor',
            'title' => 'Коэффициент пересчёта тонна/куб.м (сколько тонн весит 1 куб.м груза)',
            'type' => 'float',
            'required' => true,
            'skipInTable'  => true,
            'skipInCreate' => true,
            'skipForDriver' => true,
        ],

        [
            'name' => 'driver_salary',
            'data' => 'driver_salary',
            'title' => 'Зарплата водителя ТС, Р',
            'type' => 'float',
            'required' => true,
            'skipInTable' => true,
            'skipForDriver' => true,
        ],
    ];

    public $auto_load_relations = ['organization', 'cargo_type', 'departure_point', 'loading_unit_type', 'loading_payment_type', 'supplier', 'destination', 'unloading_unit_type', 'unloading_payment_type', 'customer', 'contractor', 'status'];

    private function getNextOrderNumber(): string {
        $cnt_orders_cur_month = Contract::whereBetween('date', [date('Y-m-01'), date('Y-m-t')])->count();
        $next_order_number = str_pad($cnt_orders_cur_month + 1, 2, '0', STR_PAD_LEFT);

        $order_number = date('y-m') . '.' . $next_order_number;

        return $order_number;
    }

    private function getCurrentOrderName(): string{
        $cargo_type      = CargoType::query()->where('id', '=', \request()->get('cargo_type_id'))->pluck('name')->first();
        $departure_point = DeparturePoint::query()->where('id', '=', \request()->get('departure_point_id'))->pluck('name')->first();
        $destination     = Destination::query()->where('id', '=', \request()->get('destination_id'))->pluck('name')->first();
        return $cargo_type."|".$departure_point."|".$destination."|Заказчик уточняется";
    }

    public function create( string $custom_view = null, array $additional_data = [] ) {
        $this->createFields['number']['default_value'] = $this->getNextOrderNumber();

        return parent::create($custom_view, $additional_data);
    }

    public function create_without_profitability(Request $request){
        //Создание заказа из доходности без создания доходности

        if (Auth::guard('admin')->check()) {
            $organization_id = $request->get('organization_id');
            $organization = Organization::findOrFail($organization_id);
        } elseif (Auth::guard('organization')->check()) {
            $organization = Auth::guard('organization')->user();
        }
        $contract = new Contract();
        $contract->date                      = date('Y-m-d',strtotime($request->get('date')));
        $contract->status_id                 = 1;
        $contract->trip_direction_id         = $request->get('trip_direction_id');
        $contract->distance                  = $request->get('distance');
        $contract->driver_salary             = $request->get('driver_salary');
        $contract->driver_salary_type_id     = $request->get('driver_salary_type_id');
        $contract->driver_salary_type_id     = $request->get('driver_salary_type_id');
        $contract->loading_unit_type_id      = $request->get('loading_price_type_id');
        $contract->unloading_unit_type_id    = $request->get('unloading_price_type_id');
        $contract->unloading_price           = $request->get('unloading_price');
        $contract->unloading_payment_type_id = 1;
        $contract->vat_in_income             = 1;
        $contract->vat_in_cargo_expenses     = 1;
        $contract->comment                   = $request->get('comment');
        $contract->organization_id           = $organization->id;
        $contract->number                    = $this->getNextOrderNumber();
        $contract->name                      = $this->getCurrentOrderName();
        $contract->conversion_factor         = 1;
        $contract->distance_price            = $organization->price_per_km;
        $contract->vat_in_fuel_expenses      = $organization->vat_in_fuel_expenses;
        try {
            $contract->save();
        }catch (\Exception $exception){
            return response()->json(['error'=>$exception->getMessage()]);
        }
        return response()->json(['success'=>true]);
    }

    public function create_from_profitability($profitability_id)
    {
        $profitability = Profitability::findOrFail($profitability_id);
        $exploded_datetime = explode(' ', $profitability['date']);
        $profitability['date'] = array_shift($exploded_datetime);
        $profitability['number'] = $this->getNextOrderNumber();
        $profitability['loading_unit_type_id'] = $profitability->loading_price_type_id;
        $profitability['unloading_unit_type_id'] = $profitability->unloading_price_type_id;

        // default values
        $profitability['status_id'] = $this->createFields['status_id']['default_value'];
        $profitability['loading_payment_type_id'] = $this->createFields['loading_payment_type_id']['default_value'];
        $profitability['unloading_payment_type_id'] = $this->createFields['unloading_payment_type_id']['default_value'];

        return parent::create(null, ['item' => $profitability]);
    }

    private function customersCargo(&$request) {
        $disabled_fields = ['loading_price', 'loading_payment_type_id', 'supplier_id'];

        foreach ($disabled_fields as $field_name) {
            unset($this->createValidate[$field_name]);
            unset($this->editValidate[$field_name]);
            $request->offsetUnset($field_name);
        }
    }

    public function store(Request $request)
    {
        $this->filterUserInput($request, 'store');

        if ($request->get('customers_cargo') === '1') {
            $this->customersCargo($request);
        }

        if (Auth::guard('admin')->check()) {
            $organization_id = $request->get('organization_id');
            $organization = Organization::findOrFail($organization_id);
        } elseif (Auth::guard('organization')->check()) {
            $organization = Auth::guard('organization')->user();
        } else {
            abort(404);
        }

        $cargo_type_id = $request->get('cargo_type_id');
        $cargo = CargoType::findOrFail($cargo_type_id);

        $this->request_data['conversion_factor'] = $cargo->ratio_ton_cubic;

        $this->request_data['distance_price'] = $organization->price_per_km;
        $this->request_data['vat_in_fuel_expenses'] = $organization->vat_in_fuel_expenses;

        return parent::store($request);
    }

    public function update(Request $request, $id)
    {
        if (isset($this->editFields['customers_cargo']) && $request->get('customers_cargo') === '1') {
            $this->customersCargo($request);
        }

        return parent::update($request, $id);
    }

    protected $append_js = ['contract-create'];
}
