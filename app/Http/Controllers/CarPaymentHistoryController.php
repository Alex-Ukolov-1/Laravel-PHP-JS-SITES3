<?php

namespace App\Http\Controllers;

use App\Models\CarPaymentHistory;
use App\Models\Car;
use App\Models\Organization;

class CarPaymentHistoryController extends CRUDController
{
    protected $model = CarPaymentHistory::class;

    protected $route = 'car_payment_history';

    protected $title = 'История оплаты автомобилей';

    protected $top_buttons = [];
    protected $row_buttons = [];

    protected $fields = [
        [
            'name'         => 'id',
            'data'         => 'id',
            'title'        => 'ID',
            'type'         => 'number',
            'skipInTable'  => true,
        ],
        [
            'name'     => 'car->organization_id',
            'data'     => 'car->organization->name',
            'title'    => 'Организация',
            'type'     => 'select',
            'source'   => Organization::class,
        ],
        [
            'name'     => 'car_id',
            'data'     => 'car->number',
            'title'    => 'Автомобиль',
            'type'     => 'select',
            'source'   => Car::class,
        ],
        [
            'name'     => 'created_at',
            'data'     => 'created_at',
            'title'    => 'Дата оплаты',
            'type'     => 'date',
        ],
        [
            'name'     => 'paid_before',
            'data'     => 'paid_before',
            'title'    => 'Оплачено до',
            'type'     => 'date',
        ],
        [
            'name'     => 'comment',
            'data'     => 'comment',
            'title'    => 'Комментарий',
            'type'     => 'textarea',
        ],
    ];

    public $auto_load_relations = ['car', 'car.organization'];
}
