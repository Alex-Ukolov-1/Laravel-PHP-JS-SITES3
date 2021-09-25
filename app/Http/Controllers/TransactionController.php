<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Organization;
use App\Models\Transaction;
use App\Repositories\TransactionTypeRepository;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Auth;

class TransactionController extends CRUDController
{
    protected $model = Transaction::class;

    protected $route = 'transactions';

    protected $title = 'Транзакции по балансу';

    protected $top_buttons = ['add'];
    protected $row_buttons = [];

    protected $fields = [
        [
            'name' => 'id',
            'data' => 'id',
            'title' => 'ID',
            'type' => 'number',
            'skipInTable' => false,
            'skipInCreate' => true,
            'skipInEdit' => true,
        ],
        [
            'name' => 'driver_id',
            'data' => 'driver->name',
            'title' => 'Водитель',
            'type' => 'select',
            'source' => Driver::class,
            'required' => true,
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
            'name' => 'type',
            'data' => 'type_name',
            'title' => 'Тип',
            'type' => 'select',
            'source' => TransactionTypeRepository::class,
            'required' => true,
        ],
        [
            'name' => 'action_id',
            'data' => 'action_id',
            'title' => 'ID Действия',
            'type' => 'integer',
        ],
        [
            'name' => 'description',
            'data' => 'description',
            'title' => 'Описание',
            'type' => 'textarea',
        ],
        [
            'name' => 'action_balance',
            'data' => 'action_balance',
            'title' => 'Сумма по балансу',
            'type' => 'float',
            'value_append' => ' руб.',
            'required' => true,
        ],
        [
            'name' => 'balance',
            'data' => 'balance',
            'title' => 'Баланс',
            'type' => 'float',
            'value_append' => ' руб.',
            'skipInCreate' => true,
        ],
        [
            'name' => 'date',
            'data' => 'date',
            'title' => 'Дата',
            'type' => 'date',
            'required' => true,
        ],
        [
            'name' => 'note',
            'data' => 'note',
            'title' => 'Примечание',
            'type' => 'textarea',
        ],
    ];

    public $auto_load_relations = ['driver', 'organization'];
}
