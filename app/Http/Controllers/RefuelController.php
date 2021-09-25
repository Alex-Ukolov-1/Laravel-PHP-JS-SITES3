<?php

namespace App\Http\Controllers;

use App\Models\Refuel;
use App\Models\Car;
use App\Models\Driver;
use App\Models\Settings\PaymentType;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;

class RefuelController extends CRUDController
{
    protected $model = Refuel::class;

    protected $route = 'refuels';

    protected $title = 'Заправки';

    protected $sort = self::SORT_BY_DATE;

    protected $fields = [
        [
            'name' => 'id',
            'data' => 'id',
            'title' => 'ID',
            'type' => 'number',
            'skipInTable' => true,
            'skipInCreate' => true,
            'skipInEdit' => true,
            'skipInShow' => true,
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
            'name' => 'date',
            'data' => 'date',
            'title' => 'Дата',
            'type' => 'datetime',
            'required' => true,
        ],
        [
            'name' => 'car_id',
            'data' => 'car->number',
            'title' => 'Машина',
            'type' => 'select',
            'source' => Car::class,
            'required' => true,
        ],
        [
            'name' => 'driver_id',
            'data' => 'driver->name',
            'title' => 'Водитель',
            'type' => 'select',
            'source' => Driver::class,
            'required' => true,
            'skipForDriver' => true,
        ],
        [
            'name' => 'fuel',
            'data' => 'fuel',
            'title' => 'Кол.топлива, л',
            'type' => 'float',
            'required' => true,
        ],
        [
            'name' => 'money',
            'data' => 'money',
            'title' => 'Сумма',
            'type' => 'float',
            'required' => true,
        ],
        [
            'name' => 'payment_type_id',
            'data' => 'payment_type->name',
            'title' => 'Форма оплаты',
            'type' => 'select',
            'source' => PaymentType::class,
            'required' => true,
        ],
        [
            'name' => 'comment',
            'data' => 'comment',
            'title' => 'Примечание',
            'type' => 'textarea',
            'skipInTable' => true,
        ],
    ];

    public $auto_load_relations = ['organization', 'car', 'driver', 'payment_type'];

    public function __construct() {
        $this->middleware(function ($request, $next) {
            if (Auth::guard('driver')->check()) {
                $this->top_buttons = ['add', 'select_all', 'deselect_all'];
            }

            return $next($request);
        });

        parent::__construct();
    }
}
