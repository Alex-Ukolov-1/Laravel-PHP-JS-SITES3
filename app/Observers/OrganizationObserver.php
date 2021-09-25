<?php

namespace App\Observers;

use App\Models\Contract;
use App\Models\Driver;
use App\Models\Organization;
use App\Models\Settings\ExpenseCategory;
use App\Models\Settings\PaymentType;
use Illuminate\Support\Facades\Cookie;
use App\Models\Settings\UnitType;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class OrganizationObserver
{
    /**
     * Handle the organization "created" event.
     *
     * @param  \App\Models\Organization  $organization
     * @return void
     */
    public function created(Organization $organization)
    {
        PaymentType::insert([
            ['organization_id' => $organization->id, 'name' => 'Наличные'],
            ['organization_id' => $organization->id, 'name' => 'Банковская карта'],
            ['organization_id' => $organization->id, 'name' => 'Топливная карта'],
            ['organization_id' => $organization->id, 'name' => 'Безналичный расчёт (с НДС 20%)'],
            ['organization_id' => $organization->id, 'name' => 'Безналичный расчёт (без НДС)'],
        ]);

        ExpenseCategory::insert([
            ['organization_id' => $organization->id, 'name' => 'Экскаваторщик'],
            ['organization_id' => $organization->id, 'name' => 'ГИБДД'],
            ['organization_id' => $organization->id, 'name' => 'Покупка зап.частей'],
            ['organization_id' => $organization->id, 'name' => 'Ремонт'],
            ['organization_id' => $organization->id, 'name' => 'Покупка товара'],
            ['organization_id' => $organization->id, 'name' => 'Выплата з/п'],
        ]);

        UnitType::insert([
            ['organization_id' => $organization->id, 'name' => 'куб.м', 'default' => 1],
            ['organization_id' => $organization->id, 'name' => 'тонна', 'default' => 0],
            ['organization_id' => $organization->id, 'name' => 'машина', 'default' => 0],
            ['organization_id' => $organization->id, 'name' => 'усл.ед', 'default' => 0],
        ]);

        Driver::create([
            'organization_id' => $organization->id,
            'name' => 'Организация',
            'email' => $organization->email,
            'password' => Hash::make('driver'.$organization->id),
            'status' => true,
            'is_organization' => true,
        ]);

        if(empty($contractDataJson = Cookie::get('contract'))){
            return false;
        }
        $org = Organization::find($organization->id);

        $contractData = json_decode($contractDataJson, true);
        $contract = new Contract();
        $contract->date                      = date('Y-m-d',strtotime($contractData['date']));
        $contract->status_id                 = 1;
        $contract->trip_direction_id         = $contractData['trip_direction_id'];
        $contract->distance                  = $contractData['distance'];
        $contract->driver_salary             = $contractData['driver_salary'];
        $contract->driver_salary_type_id     = $contractData['driver_salary_type_id'];
        $contract->loading_unit_type_id      = UnitType::getActualUnitFromProfit($contractData['loading_price_type_id'], $org->id);
        $contract->unloading_unit_type_id    = UnitType::getActualUnitFromProfit($contractData['loading_price_type_id'], $org->id);
        $contract->loading_price             = $contractData['loading_price'];
        $contract->unloading_price           = $contractData['unloading_price'];
        $contract->unloading_payment_type_id = 1;
        $contract->vat_in_income             = 1;
        $contract->vat_in_cargo_expenses     = 1;
        $contract->comment                   = $contractData['comment'];
        $contract->organization_id           = $org->id;
        $contract->number                    = Contract::getNextOrderNumber();
        $contract->number                    = strtotime('now');
        $contract->name                      = "Уточняется";
        $contract->name                      = strtotime('now')."abc";
        $contract->conversion_factor         = 1;
        $contract->distance_price            = $org->price_per_km;
        $contract->vat_in_fuel_expenses      = $org->vat_in_fuel_expenses;
        try {
            $contract->save();
        }catch (\Exception $exception){
            Log::error('error create new contract none auth', ['msg' =>$exception->getMessage()]);
        }
        Cookie::queue(Cookie::forget('contract'));

    }

}
