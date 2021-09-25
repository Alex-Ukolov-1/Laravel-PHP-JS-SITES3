<?php

namespace App\Http\Controllers;

use App\Models\Counterparty;
use App\Models\CounterpartyContact;
use App\Models\Settings\CounterpartyType;
use App\Models\Organization;
use Illuminate\Http\Request;
use Auth;

class CounterpartyController extends CRUDController
{
    protected $model = Counterparty::class;

    protected $route = 'counterparties';

    protected $title = 'Контрагенты';

    protected $row_buttons = ['contracts_by_counterparty', 'trips_by_counterparty', 'edit', 'delete'];

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
            'name' => 'counterparty_type_id',
            'data' => 'type->name',
            'title' => 'Тип контрагента',
            'type' => 'select',
            'source' => CounterpartyType::class,
            'required' => true,
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
            'name'  => 'contracts_count',
            'data'  => 'contracts_count',
            'title' => 'Заказы',
            'type'  => 'text',
            'skipInCreate' => true,
            'skipInEdit' => true,
        ],
        [
            'name'  => 'trips_count',
            'data'  => 'trips_count',
            'title' => 'Рейсы',
            'type'  => 'text',
            'skipInCreate' => true,
            'skipInEdit' => true,
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

    public $auto_load_relations = ['organization', 'type'];

    public function create( string $custom_view = null, array $additional_data = [] )
    {
        return parent::create('counterparties.create');
    }

    public function crateCounterpartyChildrens($name, $email, $phone, $note)
    {
        $names = array_slice($name, 1);
        $emails = array_slice($email, 1);
        $phones = array_slice($phone, 1);
        $notes = array_slice($note, 1);

        $mergeCounterparty = array_merge([
            'name' => $names,
            'phone' => $phones,
            'email' => $emails,
            'comment' => $notes
        ]);

        $counterpartyContact = [];
        $keys = array_keys($mergeCounterparty);

        for ($i = 0; $i < 2; $i++) {
            $temp = array_column($mergeCounterparty, $i);
            if ($temp) {
                $counterpartyContact[] = array_combine($keys, $temp);
            }
        }

        return $counterpartyContact;
    }

    public function store(Request $request)
    {
        if (Auth::guard('organization')->check()) {
            $request->merge(['organization_id' => Auth::guard('organization')->id()]);
        } elseif (Auth::guard('driver')->check()) {
            $request->merge(['organization_id' => Auth::guard('driver')->user()->organization_id]);
        }

        $request->validate($this->createValidate);

        $counterparty = Counterparty::create([
            'organization_id' => $request->organization_id,
            'counterparty_type_id' => $request->counterparty_type_id,
            'name' => $request->name[0],
            'phone' => $request->phone[0],
            'email' => $request->email[0],
            'note' => $request->note[0],
            'inn' => $request->inn,
            'bik' => $request->bik,
            'checking_account' => $request->checking_account,
        ]);

        $counterparty_contacts = $this->crateCounterpartyChildrens($request->name, $request->email, $request->phone, $request->note);

        $counterparty->contacts()->createMany($counterparty_contacts);

        return redirect('counterparties')->with('success', 'Сохранено!');
    }

    public function edit($id, $additional_data = [])
    {
        $item = $this->model->findOrFail($id);
        $item['children'] = CounterpartyContact::where('counterparty_id', $item->id)->get();

        return parent::edit($id, ['item' => $item]);
    }

    public function update(Request $request, $id)
    {
        return parent::update($request, $id);

        $request->validate($this->editValidate);

        $counterparty = Counterparty::findOrFail($id);

        $counterparty->update([
            'name' => $request->name[0],
            'phone' => $request->phone[0],
            'email' => $request->email[0],
            'note' => $request->note[0],
            'inn' => $request->inn,
            'bik' => $request->bik,
            'checking_account' => $request->checking_account,
        ]);

        $counterpartyContact = $this->crateCounterpartyChildrens($request->name, $request->email, $request->phone, $request->note);

        if (!empty($counterpartyContact)) {
            foreach ($counterpartyContact as $contact) {

                $contacts = CounterpartyContact::where('counterparty_id', $counterparty->id)->first();

                if ($contacts) {
                    $contacts->update([
                       'counterparty_id' => $counterparty->id,
                       'name' => $contact['name'],
                       'phone' => $contact['phone'],
                       'email' => $contact['email'],
                       'comment' => $contact['comment'],
                    ]);
                } else {
                    $contacts = CounterpartyContact::create([
                        'counterparty_id' => $counterparty->id,
                        'name' => $contact['name'],
                        'phone' => $contact['phone'],
                        'email' => $contact['email'],
                        'comment' => $contact['comment'],
                    ]);
                }
            }
        } else {
            CounterpartyContact::where('counterparty_id', $counterparty->id)->delete();
        }

        return redirect('counterparties')->with('success', 'Сохранено!');
    }

    public function destroy($id)
    {
        CounterpartyContact::where('counterparty_id', $id)->delete();

        return parent::destroy($id);
    }

    public function suppliersJson(Request $request)
    {
        return response()->json(
            Counterparty::suppliers()
                ->select(['id', 'name as text'])
                ->where('name', 'like', '%'.$request->input('q').'%')
                ->get()
        );
    }

    public function customersJson(Request $request)
    {
        return response()->json(
            Counterparty::customers()
                ->select(['id', 'name as text'])
                ->where('name', 'like', '%'.$request->input('q').'%')
                ->get()
        );
    }

    public function contractorsJson(Request $request)
    {
        return response()->json(
            Counterparty::contractors()
                ->select(['id', 'name as text'])
                ->where('name', 'like', '%'.$request->input('q').'%')
                ->get()
        );
    }

}
