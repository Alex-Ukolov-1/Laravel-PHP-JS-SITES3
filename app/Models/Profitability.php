<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\CRUDFunctions;
use Auth;
use App\Models\Traits\FilterByRole;

class Profitability extends Model
{
	use CRUDFunctions;
	use FilterByRole;

	protected $table = 'profitability';

	protected $fillable = [
		'organization_id', 'driver_id', 'name', 'date', 'distance', 'loading_volume', 'loading_volume_type_id', 'loading_price',
		'loading_price_type_id', 'unloading_volume', 'unloading_volume_type_id', 'unloading_price',
		'unloading_price_type_id', 'conversion_factor', 'additional_overhead', 'trip_direction_id', 'price_of_fuel',
		'average_fuel_consumption', 'fixed_overhead', 'driver_salary', 'driver_salary_type_id', 'driver_salary_direction_id',
		'with_taxes', 'vat_in_income', 'vat_in_fuel_expenses', 'vat_in_cargo_expenses', 'vat_in_fixed_overhead', 'vat_in_additional_overhead', 'comment',
		'shipping_cost', 'profit',
	];

	public $timestamps = false;

	public function driver() {
	    return $this->hasOne('App\Models\Driver');
	}

	public function organization() {
        return $this->belongsTo('App\Models\Organization');
	}

    public function getDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }

	public $manyToManyRelations = [];
}
