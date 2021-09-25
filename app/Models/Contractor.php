<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Contractor extends Counterparty
{
	protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('suppliers', function(Builder $builder) {
           $builder->where('counterparty_type_id', self::CONTRACTOR);
        });

        static::creating(function($model) {
           $model->counterparty_type_id = self::CONTRACTOR;
        });
    }
}
