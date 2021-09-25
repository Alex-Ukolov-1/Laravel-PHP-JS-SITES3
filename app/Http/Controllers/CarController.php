<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\Organization;
use App\Models\CarPaymentHistory;
use Auth;

class CarController extends CRUDController
{
    protected $model = Car::class;

    protected $route = 'cars';

    protected $title = 'Автомобили';

    protected $top_buttons = ['add', 'delete', 'pay_car', 'select_all', 'deselect_all'];
    protected $row_buttons = ['pay_car', 'edit', 'delete'];

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
            'name' => 'number',
            'data' => 'number',
            'title' => 'Номер (Описание)',
            'type' => 'string',
            'required' => true,
        ],
        [
            'name' => 'paid_before',
            'data' => 'paid_before',
            'title' => 'Оплачен до',
            'type' => 'date',
            'skipInCreate' => true,
            'skipInEdit' => true,
        ],
        [
            'name' => 'paid_before',
            'data' => 'paid_before',
            'title' => 'Оплачен до',
            'type' => 'date',
            'skipInTable' => true,
            'forAdminOnly' => true,
            'onchange' => 'showCommentTextareaInCarEdit(this)',
        ],
        [
            'name' => 'status',
            'data' => 'status',
            'title' => 'Статус',
            'type' => 'boolean',
            'boolean_turn_on' => 'Включен',
            'boolean_turn_off' => 'Отключен',
            'required' => true,
            'default_value' => 1,
        ],
    ];

    public function list(Request $request, $main_field_name = 'number') {
        return \response()->json(
            $this->model->select(['id', "$main_field_name as text"])
                ->where($main_field_name, 'like', '%'.$request->input('q').'%')
                ->paid()
                ->where('status', 1)
                ->applyScopes()->getQuery()->get()
        );
    }

    private function addCarPaymentHistory(Request $request, string $type) {

        $paid_before = $request->get('paid_before');
        $comment = $request->get('comment');

        if (($type === 'store' && !empty($paid_before))
        || ($type === 'update' && !empty($paid_before) && ($this->item->wasChanged('paid_before') || !empty($comment)))) {
            CarPaymentHistory::create([
                'car_id' => $this->item->id,
                'created_at' => date('Y-m-d H:i:s'),
                'comment' => $comment,
                'paid_before' => $paid_before,
            ]);
        }

    }

    public function store(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $this->createFields['comment'] = [];
        }

        $response = parent::store($request);

        $this->addCarPaymentHistory($request, 'store');

        return $response;
    }

    public function update(Request $request, $id)
    {
        if (Auth::guard('admin')->check()) {
            $this->editFields['comment'] = [];
        }

        $response = parent::update($request, $id);

        $this->addCarPaymentHistory($request, 'update');

        return $response;
    }
}
