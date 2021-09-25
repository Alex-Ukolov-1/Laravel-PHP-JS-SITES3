<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Payments\YooPayment;

class YooPaymentController extends CRUDController
{

  protected $model = YooPayment::class;

  protected $route = 'yoo_payments';

  protected $title = 'Платежи';

  protected $top_buttons = [];
  protected $row_buttons = [];

  protected $fields = [
    [
        'name' => 'id',
        'data' => 'id',
        'title' => 'ID',
        'type' => 'number',
        'skipInTable' => true,
    ],
    [
        'name' => 'created_at',
        'data' => 'created_at',
        'title' => 'Дата',
        'type' => 'datetime',
    ],
    [
        'name' => 'organization_id',
        'data' => 'organization->name',
        'title' => 'Организация',
        'type' => 'select',
        'source' => Organization::class,
    ],
    [
        'name' => 'amount',
        'data' => 'amount',
        'title' => 'Сумма',
        'type' => 'float',
    ],
    [
        'name' => 'detail',
        'data' => 'detail_hr',
        'title' => 'Оплачено',
        'type' => 'textarea',
    ],
    [
        'name' => 'status',
        'data' => 'status_hr_extended',
        'title' => 'Статус',
        'type' => 'select',
        'source' => YooPayment::STATUSES,
    ],
  ];

}
