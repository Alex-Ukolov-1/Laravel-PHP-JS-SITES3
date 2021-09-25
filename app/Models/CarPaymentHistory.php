<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\CRUDFunctions;

class CarPaymentHistory extends Model
{
	use CRUDFunctions;

	protected $table = 'car_payment_history';

	protected $fillable = [
	  'car_id', 'payment_id', 'created_at', 'comment', 'paid_before',
	];

	public $timestamps = false;

    public static $model_name = 'История оплаты автомобилей';
    public $model_relations = [];

    public function car() {
    	return $this->belongsTo('App\Models\Car');
    }

    public function payment() {
    	return $this->belongsTo('App\Models\YooPayment');
    }
}
