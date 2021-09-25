<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Organization;
use App\Models\CounterpartyContact;
use Illuminate\Http\Request;

class CustomerController extends CRUDController
{
    protected $model = Customer::class;

    protected $route = 'customers';

    protected $title = 'Заказчики';

    protected $datatable_plugins = ['filter_customers_by_trips'];

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
            'title' => 'Название',
            'type' => 'text',
            'required' => true,
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
            'name' => 'inn',
            'data' => 'inn',
            'title' => 'ИНН',
            'type' => 'text',
        ],
        [
            'name' => 'bik',
            'data' => 'bik',
            'title' => 'БИК',
            'type' => 'text',
        ],
        [
            'name' => 'checking_account',
            'data' => 'checking_account',
            'title' => 'Расчётный счёт',
            'type' => 'text',
        ],
        [
            'name' => 'note',
            'data' => 'note',
            'title' => 'Примечание',
            'type' => 'textarea',
        ],
        [
            'name' => 'total_by_trips',
            'data' => 'total_by_trips',
            'title' => 'Сумма за рейсы',
            'type' => 'float',
            'skipInCreate' => true,
            'skipInEdit' => true,
        ],
        [
            'name' => 'paid',
            'data' => 'paid',
            'title' => 'Оплатил',
            'type' => 'float',
            'skipInCreate' => true,
            'skipInEdit' => true,
        ],
        [
            'name' => 'debt',
            'data' => 'debt',
            'title' => 'Должен',
            'type' => 'float',
            'skipInCreate' => true,
            'skipInEdit' => true,
        ],
    ];

    public function list(Request $request, $main_field_name = 'name') {
        return \response()->json(
            $this->model->select(['id', "$main_field_name as text"])
                ->where($main_field_name, 'like', '%'.$request->input('q').'%')
                ->where('status', '=', 1)
                ->applyScopes()->getQuery()->get()
        );
    }

    public function applyCustomFilters(&$fields, &$query) {
        if (!empty($fields['has_trips_date_from']) || !empty($fields['has_trips_date_to'])) {
            $date_from = $fields['has_trips_date_from'] ?? '';
            $date_to = $fields['has_trips_date_to'] ?? '';

            $query->whereHas('contracts', function ($query) use ($date_from, $date_to) {
                $query->whereHas('trips', function ($query) use ($date_from, $date_to) {
                    if (!empty($date_from)) $query->whereDate('date', '>=', $date_from);
                    if (!empty($date_to)) $query->whereDate('date', '<=', $date_to);
                });
            });

            unset($fields['has_trips_date_from']);
            unset($fields['has_trips_date_to']);
        }
    }

    public function edit($id, $additional_data = [])
    {
        $item = $this->model->findOrFail($id);

        $item['children'] = CounterpartyContact::where('counterparty_id', $item->id)->get();

        return view('customers.edit', [
            'fields' => $this->editFields,
            'item' => $item,
            'title' => $this->title.'. Редактирование',
            'route' => $this->route,
            'form_buttons' => $this->form_buttons,
            'type' => 'edit',
            'payments' => $item->incomes,
            'append_js' => $this->append_js,
        ]);
    }

}
