<?php

namespace App\Http\Controllers;

use App\Models\CounterpartyContact;
use App\Http\Controllers\Traits\FieldsGetters;

class CounterpartyContactController extends CRUDController
{
    use FieldsGetters;

    protected $model = CounterpartyContact::class;

    protected $route = '';

    protected $title = '';

    protected $fields = [
        [
            'name' => 'name',
            'data' => 'name',
            'title' => 'Название',
            'type' => 'text',
        ],

        [
            'name' => 'email',
            'data' => 'email',
            'title' => 'E-Mail',
            'type' => 'text',
        ],

        [
            'name' => 'phone',
            'data' => 'phone',
            'title' => 'Телефон',
            'type' => 'text',
        ],

        [
            'name' => 'comment',
            'data' => 'comment',
            'title' => 'Комментарий',
            'type' => 'textarea',
        ],
    ];
}
