<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\CRUDFunctions;
use Auth;
use App\Models\Traits\FilterByRole;

class Expense extends Model
{
	use CRUDFunctions;
	use FilterByRole;

	protected $fillable = [
	  'organization_id', 'date', 'car_id', 'driver_id', 'expense_category_id', 'money', 'supplier_id', 'cargo_type_id', 'cargo_unit_type_id', 'cargo_amount', 'comment', 'payment_type_id',
	];

	public $timestamps = false;

    public static $model_name = 'Расход';
    public $model_relations = [];

	public function organization() {
    return $this->belongsTo('App\Models\Organization');
	}

	public function car() {
    return $this->belongsTo('App\Models\Car');
	}

	public function driver() {
    return $this->belongsTo('App\Models\Driver');
	}

	public function expense_category() {
	  return $this->belongsTo('App\Models\Settings\ExpenseCategory');
	}

	public function supplier() {
	  return $this->belongsTo('App\Models\Counterparty');
	}

	public function cargo_type() {
	  return $this->belongsTo('App\Models\Settings\CargoType');
	}

	public function cargo_unit_type() {
	  return $this->belongsTo('App\Models\Settings\UnitType');
	}

    public function payment_type() {
        return $this->belongsTo('App\Models\Settings\PaymentType');
    }

}
