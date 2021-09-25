<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\CRUDFunctions;
use Auth;

class Payment extends Model
{
	protected $fillable = [
	  'customer_id', 'incoming_sum'
	];

	public function customer()
    {
        return $this->hasOne('App\Models\Customer', 'id', 'customer_id');
    }

}
