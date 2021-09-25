<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profitability;
use App\Models\Settings\UnitType;
use App\Models\Settings\SalaryType;
use App\Models\Settings\TripDirectionType;
use Auth;
use App\Models\Organization;

class ProfitabilityController extends CRUDController
{
    protected $model = Profitability::class;

    protected $route = 'profitability';

    protected $title = 'Доходность заказов';

    protected $sort = self::SORT_BY_DATE;

    protected $buttons = ['cancel', 'save', 'save_and_exit'];

    protected $form_buttons = ['save', 'save_and_exit', 'cancel'];

    protected $append_js = ['profitability'];

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
        ],
        [
            'name' => 'date',
            'data' => 'date',
            'title' => 'Дата',
            'type' => 'date',
            'required' => true,
        ],
        [
            'name' => 'distance',
            'data' => 'distance',
            'title' => 'Расстояние рейса, км',
            'type' => 'float',
            'required' => true,
        ],
        [
            'title' => 'Покупка',
            'type' => 'subtitle',
            'skipInTable' => true,
        ],
        [
            'name' => 'loading_volume',
            'data' => 'loading_volume',
            'title' => 'Объём погрузки за рейс, ед.',
            'type' => 'float',
            'required' => true,
            'view' => 'start_group',
            'skipInTable' => true,
        ],
        [
            'name' => 'loading_volume_type_id',
            'data' => 'loading_volume_type_id',
            'type' => 'select',
            'source' => UnitType::class,
            'required' => true,
            'view' => 'end_group',
            'skipInTable' => true,
        ],
        [
            'name' => 'loading_price',
            'data' => 'loading_price',
            'title' => 'Цена за ед.груза, Р.',
            'type' => 'float',
            'required' => true,
            'view' => 'start_group',
            'skipInTable' => true,
        ],
        [
            'name' => 'loading_price_type_id',
            'data' => 'loading_price_type_id',
            'type' => 'select',
            'source' => UnitType::class,
            'required' => true,
            'view' => 'end_group',
            'skipInTable' => true,
        ],
        [
            'title' => 'Продажа',
            'type' => 'subtitle',
            'skipInTable' => true,
        ],
        [
            'name' => 'unloading_volume',
            'data' => 'unloading_volume',
            'title' => 'Объём отгрузки заказчику за рейс, ед.',
            'type' => 'float',
            'required' => true,
            'view' => 'start_group',
            'skipInTable' => true,
        ],
        [
            'name' => 'unloading_volume_type_id',
            'data' => 'unloading_volume_type_id',
            'type' => 'select',
            'source' => UnitType::class,
            'required' => true,
            'view' => 'end_group',
            'skipInTable' => true,
        ],
        [
            'name' => 'unloading_price',
            'data' => 'unloading_price',
            'title' => 'Цена за ед.груза, Р',
            'type' => 'float',
            'required' => true,
            'view' => 'start_group',
        ],
        [
            'name' => 'unloading_price_type_id',
            'data' => 'unloading_price_type_id',
            'type' => 'select',
            'source' => UnitType::class,
            'required' => true,
            'view' => 'end_group',
            'skipInTable' => true,
        ],
        [
            'title' => '',
            'type' => 'subtitle',
            'skipInTable' => true,
        ],
        [
            'title' => 'Настройки',
            'type' => 'subtitle',
            'skipInTable' => true,
        ],
        [
            'name' => 'trip_direction_id',
            'data' => 'trip_direction_id',
            'title' => 'Понятие "РЕЙС"',
            'type' => 'select',
            'source' => TripDirectionType::class,
            'required' => true,
            'skipInTable' => true,
        ],
        [
            'name' => 'conversion_factor',
            'data' => 'conversion_factor',
            'title' => 'Коэффициент пересчёта тонна/куб.м (сколько тонн весит 1 куб.м груза)',
            'type' => 'float',
            'default_value' => '1.00',
            'required' => true,
            'skipInTable' => true,
        ],
        [
            'name' => 'additional_overhead',
            'data' => 'additional_overhead',
            'title' => 'Дополнительные накладные расходы, руб.',
            'type' => 'float',
            'default_value' => '0.00',
            'required' => true,
            'skipInTable' => true,
        ],
        [
            'name' => 'price_of_fuel',
            'data' => 'price_of_fuel',
            'title' => 'Цена топлива, руб./л',
            'type' => 'float',
            'required' => true,
            'skipInTable' => true,
        ],
        [
            'name' => 'average_fuel_consumption',
            'data' => 'average_fuel_consumption',
            'title' => 'Средний расход топлива ТС, л/100 км',
            'type' => 'float',
            'required' => true,
            'skipInTable' => true,
        ],
        [
            'name' => 'driver_salary',
            'data' => 'driver_salary',
            'title' => 'Заработная плата водителя ТС за рейс, руб.',
            'type' => 'float',
            'required' => true,
            'skipInTable' => true,
        ],
        [
            'name' => 'with_taxes',
            'data' => 'with_taxes',
            'title' => 'Учесть в з/п водителя социальные налоги и НДФЛ',
            'type' => 'boolean',
            'required' => true,
            'default_value' => 0,
            'skipInTable' => true,
        ],
        [
            'title' => 'Учёт НДС',
            'type' => 'subtitle',
            'skipInTable' => true,
        ],
        [
            'name' => 'vat_in_income',
            'data' => 'vat_in_income',
            'title' => 'В доходах',
            'table_title' => 'Учёт НДС в доходах',
            'type' => 'boolean',
            'required' => true,
            'default_value' => 1,
        ],
        [
            'name' => 'vat_in_fuel_expenses',
            'data' => 'vat_in_fuel_expenses',
            'title' => 'В расходах на топливо',
            'type' => 'boolean',
            'required' => true,
            'default_value' => 1,
            'skipInTable' => true,
        ],
        [
            'name' => 'vat_in_cargo_expenses',
            'data' => 'vat_in_cargo_expenses',
            'title' => 'В расходах на груз',
            'type' => 'boolean',
            'required' => true,
            'default_value' => 1,
            'skipInTable' => true,
        ],
        [
            'name' => 'vat_in_fixed_overhead',
            'data' => 'vat_in_fixed_overhead',
            'title' => 'В постоянных накладных расходах',
            'type' => 'boolean',
            'required' => true,
            'default_value' => 0,
            'skipInTable' => true,
        ],
        [
            'name' => 'vat_in_additional_overhead',
            'data' => 'vat_in_additional_overhead',
            'title' => 'В дополнительных накладных расходах',
            'type' => 'boolean',
            'required' => true,
            'default_value' => 0,
            'skipInTable' => true,
        ],
        [
            'title' => '',
            'type' => 'subtitle',
            'skipInTable' => true,
        ],
        [
            'name' => 'comment',
            'data' => 'comment',
            'title' => 'Примечания',
            'type' => 'textarea',
            'required' => false,
        ],
        [
            'name' => 'shipping_cost',
            'data' => 'shipping_cost',
            'title' => 'Цена доставки',
            'type' => 'float',
            'value_append' => ' куб.м/км',
            'skipInCreate' => true,
            'skipInEdit' => true,
        ],
        [
            'name' => 'profit',
            'data' => 'profit',
            'title' => 'Прибыль за рейс',
            'type' => 'float',
            'value_append' => ' руб.',
            'skipInCreate' => true,
            'skipInEdit' => true,
        ],
        [
            'name' => 'name',
            'data' => 'name',
            'title' => 'Название',
            'type' => 'text',
            'required' => true,
        ],
    ];

    public function store(Request $request)
    {
        $this->filterUserInput($request, 'store');

        $request->validate($this->createValidate);

        $data = $request->all();

        $this->request_data = array_merge($this->request_data, $this->calc($data));

        return parent::store($request);
    }

    public function update(Request $request, $id)
    {
        $this->filterUserInput($request, 'update');

        $request->validate($this->editValidate);

        $data = $request->all();

        $this->request_data = array_merge($this->request_data, $this->calc($data));

        return parent::update($request, $id);
    }

    private function toFloat($value) {
        return round($value, 2);
    }

    public function calc(&$data) {

        /* Main */

        if (is_nan($data['conversion_factor']) || $data['conversion_factor'] === '') {
            $data['unloadingVolume'] = null;
            $data['unloadingPrice'] = null;
            $data['loadingPrice'] = null;
            $data['loadingVolume'] = null;

            return;
        }

        $data['unloadingVolume'] = $data['unloading_volume']; // Объём отгрузки заказчику за рейс (грузоподъёмность ТС)
        $data['unloadingPrice'] = $data['unloading_price']; // Выручка за доставку единицы груза
        $data['loadingVolume'] = $data['loading_volume']; // Объём приобретённого груза за рейс
        $data['loadingPrice'] = $data['loading_price']; // Цена покупки единицы груза

        if ($data['unloading_volume_type_id'] == '1') $data['unloadingVolume'] = $this->toFloat($data['unloadingVolume'] / $data['conversion_factor']);
        if ($data['unloading_price_type_id'] == '1') $data['unloadingPrice'] = $this->toFloat($data['unloadingPrice'] / $data['conversion_factor']);
        if ($data['loading_price_type_id'] == '1') $data['loadingPrice'] = $this->toFloat($data['loadingPrice'] / $data['conversion_factor']);
        if ($data['loading_volume_type_id'] == '1') $data['loadingVolume'] = $this->toFloat($data['loadingVolume'] / $data['conversion_factor']);

        /* Intermediate results */

        // Общий пробег за рейс
        if ($data['trip_direction_id'] == '1') {
            $data['fullTripDistance'] = $this->toFloat($data['distance'] * 2);
        } else {
            $data['fullTripDistance'] = $data['distance'];
        }

        $data['revenueForTheTrip'] = $this->toFloat($data['unloadingVolume'] * $data['unloadingPrice']); // Выручка за рейс
        $data['costOfTheCargo'] = $this->toFloat($data['loadingVolume'] * $data['loadingPrice']); // Затраты на покупку груза за рейс

        $data['driversSalaryForTheTrip'] = $data['driver_salary']; // Заработная плата водителя за рейс

        $data['priceOfFuelForTheTrip'] = $this->toFloat($data['price_of_fuel'] * $data['fullTripDistance'] * $data['average_fuel_consumption'] / 100); // Цена топлива за рейс
        $data['additionalOverhead'] = $data['additional_overhead']; // Дополнительные накдалные расходы на рейс

        /* Results */

        $data['revenueForTheTripWithoutVAT'] = $data['revenueForTheTrip'];
        $data['costOfTheCargoWithoutVAT'] = $data['costOfTheCargo'];
        $data['driversSalaryForTheTripWithoutVAT'] = $data['driversSalaryForTheTrip'];
        $data['priceOfFuelForTheTripWithoutVAT'] = $data['priceOfFuelForTheTrip'];
        $data['additionalOverheadWithoutVAT'] = $data['additionalOverhead'];

        if ($data['vat_in_income'] == '1') $data['revenueForTheTrip'] = $this->toFloat($data['revenueForTheTrip'] / 1.2);
        if ($data['vat_in_cargo_expenses'] == '1') $data['costOfTheCargo'] = $this->toFloat($data['costOfTheCargo'] / 1.2);
        if ($data['with_taxes'] == '1') $data['driversSalaryForTheTrip'] = $this->toFloat($data['driversSalaryForTheTrip'] * 1.432);
        if ($data['vat_in_fuel_expenses'] == '1') $data['priceOfFuelForTheTrip'] = $this->toFloat($data['priceOfFuelForTheTrip'] / 1.2);
        if ($data['vat_in_fixed_overhead'] == '1') $data['fixedOverhead'] = $this->toFloat($data['fixedOverhead'] / 1.2);
        if ($data['vat_in_additional_overhead'] == '1') $data['additionalOverhead'] = $this->toFloat($data['additionalOverhead'] / 1.2);

        $data['shipping_cost'] = $this->toFloat((($data['revenueForTheTrip'] / $data['unloadingVolume']) - ($data['costOfTheCargo'] / $data['unloadingVolume'])) / $data['distance']);
        $data['profit'] = $this->toFloat($data['revenueForTheTrip'] - ($data['costOfTheCargo'] + $data['driversSalaryForTheTrip'] + $data['priceOfFuelForTheTrip'] + $data['additionalOverhead']));

        return $data;
    }

    public function edit($id, $additional_data = []){
        array_push($this->form_buttons, 'create_contract');

        return parent::edit($id, $additional_data);
    }

    public function create( string $custom_view = null, array $additional_data = [] ) {
        if (auth('organization')->check()) {
            $organization = Auth::guard('organization')->user();

            $this->createFields['price_of_fuel']['default_value'] = $organization->fuel_price;
            $this->createFields['average_fuel_consumption']['default_value'] = $organization->average_fuel_consumption;
        }

        $this->createFields['name']['default_value'] = $this->getNextRecordId();

        array_push($this->form_buttons, 'create_contract');

        return parent::create($custom_view, $additional_data);
    }

    private function getNextRecordId() {
        $id = $this->model->select('id')->pluck('id')->last();

        return ++$id;
    }

}
