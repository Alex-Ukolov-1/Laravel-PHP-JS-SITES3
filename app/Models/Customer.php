<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Customer extends Counterparty
{
    public $model_relations = ['incomes', 'contracts'];

	protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('customers', function(Builder $builder) {
           $builder->where('counterparty_type_id', self::CUSTOMER);
        });

        static::creating(function($model) {
           $model->counterparty_type_id = self::CUSTOMER;
        });
    }

    public function getTotalByTripsAttribute()
    {
        if (!isset($this->cached_total_by_trips)) {
            $SQL = "SELECT SUM(total_trip) as total_by_trip FROM (
                        SELECT unloading_cargo_amount, contract_id, contracts.unloading_price, (unloading_cargo_amount * contracts.unloading_price) as total_trip FROM (
                            SELECT trips.id, unloading_cargo_amount, contract_id
                            FROM trips
                            WHERE contract_id IN (
                                    SELECT id from contracts WHERE contracts.customer_id = (
                                        SELECT id from counterparties WHERE counterparties.counterparty_type_id = 1 AND counterparties.id = '".$this->id."'
                                    )
                                )
                            ) as t, contracts
                            WHERE contracts.id = contract_id
                        ) as t";
            $resultQuery = DB::select($SQL);

            if (empty($resultQuery)) $this->cached_total_by_trips = 0;
            else $this->cached_total_by_trips = (int)$resultQuery[0]->total_by_trip;
        }

        return $this->cached_total_by_trips;
    }


    public function getPaidAttribute()
    {
        if (!isset($this->cached_paid)) {
            $this->cached_paid = (Income::where('customer_id', $this->id)->sum('money')) ? : 0;
        }

        return $this->cached_paid;
    }

    public function getDebtAttribute()
    {
        return $this->total_by_trips - $this->paid;
    }

    public function incomes()
    {
        return $this->hasMany('App\Models\Income', 'customer_id', 'id');
    }

    public function contracts() {
        return $this->hasMany('App\Models\Contract');
    }

    public $custom_fields = ['total_by_trips', 'paid', 'debt'];
}
