<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Supplier extends Counterparty
{
	protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('suppliers', function(Builder $builder) {
           $builder->where('counterparty_type_id', self::SUPPLIER);
        });

        static::creating(function($model) {
           $model->counterparty_type_id = self::SUPPLIER;
        });
    }
}
