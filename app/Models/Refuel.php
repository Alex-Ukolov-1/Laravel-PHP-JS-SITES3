<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\CRUDFunctions;
use Auth;
use App\Models\Traits\FilterByRole;

class Refuel extends Model
{
	use CRUDFunctions;
	use FilterByRole;

	protected $fillable = [
	  'organization_id', 'date', 'car_id', 'driver_id', 'fuel', 'money', 'payment_type_id', 'comment',
	];

	public $timestamps = false;

	public static $model_name = 'Заправки';

	public function car() {
    return $this->belongsTo('App\Models\Car');
	}

	public function driver() {
    return $this->belongsTo('App\Models\Driver');
	}

	public function payment_type() {
    return $this->belongsTo('App\Models\Settings\PaymentType');
	}

	public function organization() {
    return $this->belongsTo('App\Models\Organization');
	}
}
