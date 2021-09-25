<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Income;
use App\Models\Payment;
use App\Models\User;

class PaymentController extends CRUDController
{
    protected $model = Payment::class;

    protected $route = 'payments';

    protected $title = 'Платежи';

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
            'name' => 'customer_id',
            'data' => 'customer_id',
            'title' => 'Заказчик',
            'type' => 'select',
            'source' => Customer::class,
            'required' => true,
        ],
    ];
}
