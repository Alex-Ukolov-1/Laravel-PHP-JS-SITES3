<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CarPaymentHistory;
use App\Models\Settings\SettingsModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\CRUDFunctions;
use App\Models\Traits\FilterByRole;

class Car extends Model
{
	use SoftDeletes;
	use CRUDFunctions;
	use FilterByRole;

	protected $fillable = [
	  'organization_id', 'number', 'paid_before', 'status',
	];

	public $timestamps = false;

    public static $model_name = 'Машина';
    public $model_relations = ['trips', 'incomes', 'expenses', 'refuels', 'drivers'];

	/*
		RELATIONS
	*/

	public function organization() {
	    return $this->belongsTo('App\Models\Organization');
	}

	public function trips(){
	    return $this->hasMany('App\Models\Trip');
    }

    public function incomes(){
        return $this->hasMany('App\Models\Income');
    }

    public function expenses(){
	    return $this->hasMany('App\Models\Expense');
    }

    public function refuels(){
        return $this->hasMany('App\Models\Refuel');
    }

    public function drivers(){
        return $this->hasMany('App\Models\Driver');
    }

    /*
    	SCOPES
    */

	public function scopeList($query, $main_field_name = 'number') {
		return $query->paid()->where('status', 1)->pluck($main_field_name, 'id');
	}

	public function scopeFullList($query, $main_field_name = 'number') {
		return $query->pluck($main_field_name, 'id');
	}

	public function scopeUnpaid($query) {
		return $query->whereNull('paid_before')->orWhereDate('paid_before', '<', date('Y-m-d'));
	}

	public function scopePaid($query) {
		return $query->whereDate('paid_before', '>=', date('Y-m-d'));
	}

	/*
		ATTRIBUTES
	*/

	public function getIsPaidAttribute() {
		return $this->paid_before >= date('Y-m-d');
	}

	/*
		METHODS
	*/

	public function pay($payment) {
		$price = $payment->price_per_car;

		if ($this->is_paid === false) { // Оплата или доплата
		  $paid_before = date('Y-m-d', strtotime(' +' . $payment->months . ' month'));
		} else { // Продление
		  $paid_before = date('Y-m-d', strtotime($this->paid_before . ' +' . $payment->months . ' month'));
		}

		$this->update([
		  'last_payment_error' => null,
		  'paid_before' => $paid_before,
		]);

		CarPaymentHistory::create([
		    'car_id' => $this->id,
		    'payment_id' => $payment->id,
		    'created_at' => date('Y-m-d H:i:s'),
		    'paid_before' => $paid_before,
		]);
	}

	public function deactivate() {
	  $this->update([ 'paid_before' => null ]);
	}

	public function reset() {
	  $this->update([ 'paid_before' => null ]);
	}
}
