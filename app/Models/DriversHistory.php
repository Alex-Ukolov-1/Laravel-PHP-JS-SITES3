<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\CRUDFunctions;

class DriversHistory extends Model
{
  protected $table = 'drivers_history';

	use CRUDFunctions;

	protected $fillable = [
		'car_id',
		'driver_id',
	];

	public function car() {
	    return $this->belongsTo('App\Models\Car')->withTrashed();
	}

	public function driver() {
	    return $this->belongsTo('App\Models\Driver');
	}
}
