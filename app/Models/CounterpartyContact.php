<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\CRUDFunctions;

class CounterpartyContact extends Model
{
	use CRUDFunctions;

	protected $fillable = [
		'counterparty_id', 'name', 'phone', 'email', 'comment',
	];

	public $timestamps = false;
}
