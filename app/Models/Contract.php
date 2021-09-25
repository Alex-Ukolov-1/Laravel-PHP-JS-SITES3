<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\CRUDFunctions;
use Auth;
use DB;
use App\Models\Traits\FilterByRole;

class Contract extends Model
{
	use CRUDFunctions;
	use FilterByRole;

	protected $fillable = [
		'organization_id', 'number', 'name', 'date', 'customer_id', 'contractor_id', 'status_id',
		'cargo_type_id', 'trip_direction_id', 'distance', 'conversion_factor', 'driver_salary', 'driver_salary_type_id', 'customers_cargo',
		'departure_point_id', 'loading_unit_type_id', 'loading_price', 'loading_payment_type_id', 'supplier_id', 'destination_id',
		'unloading_unit_type_id', 'unloading_price', 'unloading_payment_type_id', 'vat_in_income', 'vat_in_fuel_expenses',
		'vat_in_cargo_expenses', 'comment','distance_price'
	];

	public $timestamps = false;

	public static $model_name = 'Заказ';
	public $model_relations = ['trips'];

	public function scopeList($query, $main_field_name = 'name') {
		return $query->orderBy('number', 'DESC')->select(DB::raw("id, CONCAT(number, ' ', name) AS name"))->pluck('name', 'id');
	}

	public function scopeFullList($query, $main_field_name = 'name') {
		return $query->orderBy('number', 'DESC')->select(DB::raw("id, CONCAT(number, ' ', name) AS name"))->pluck('name', 'id');
	}

	public function getNameForListAttribute() {
		return $this->number . ' ' . $this->name;
	}

	public function customer() {
	    return $this->belongsTo('App\Models\Customer');
	}

	public function contractor() {
	    return $this->belongsTo('App\Models\Contractor');
	}

	public function supplier() {
	    return $this->belongsTo('App\Models\Supplier');
	}

	public function loading_payment_type() {
	    return $this->belongsTo('App\Models\Settings\PaymentType');
	}

	public function unloading_payment_type() {
	    return $this->belongsTo('App\Models\Settings\PaymentType');
	}

	public function status() {
	    return $this->belongsTo('App\Models\Settings\Status');
	}

	public function organization() {
	    return $this->belongsTo('App\Models\Organization');
	}

	public function cargo_type() {
	    return $this->belongsTo('App\Models\Settings\CargoType');
	}

	public function departure_point() {
	    return $this->belongsTo('App\Models\Settings\DeparturePoint');
	}

	public function destination() {
	    return $this->belongsTo('App\Models\Settings\Destination');
	}

	public function loading_unit_type() {
	    return $this->belongsTo('App\Models\Settings\UnitType');
	}

	public function unloading_unit_type() {
	    return $this->belongsTo('App\Models\Settings\UnitType');
	}

	public function trips() {
	    return $this->hasMany('App\Models\Trip');
	}

    public static function getNextOrderNumber(): string {
        $cnt_orders_cur_month = Contract::whereBetween('date', [date('Y-m-01'), date('Y-m-t')])->count();
        $next_order_number = str_pad($cnt_orders_cur_month + 1, 2, '0', STR_PAD_LEFT);

        $order_number = date('y-m') . '.' . $next_order_number;

        return $order_number;
    }

    public function getTotalDriverSalaryAttribute()
    {
        return $this->driver_salary ?: 0;
    }

    public $custom_fields = ['name_for_list'];

	public $appends = ['total_driver_salary'];
}
