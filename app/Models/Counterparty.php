<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\CRUDFunctions;
use App\Models\Traits\FilterByRole;
use Illuminate\Support\Facades\DB;

class Counterparty extends Model
{
	use CRUDFunctions;
	use FilterByRole;

	const CUSTOMER = 1;
	const SUPPLIER = 2;
	const CONTRACTOR = 3;

	protected $table = 'counterparties';

	protected $fillable = [
	  'organization_id', 'counterparty_type_id', 'name', 'email', 'phone', 'inn', 'bik', 'checking_account', 'note', 'status',
	];

	public $timestamps = false;

  	public static $model_name = 'Контрагенты';
  	public $model_relations = ['contracts', 'trips'];

  	/*
  		RELATIONS
  	*/

	public function organization() {
        return $this->belongsTo('App\Models\Organization');
	}

	public function type() {
		return $this->belongsTo('App\Models\Settings\CounterpartyType', 'counterparty_type_id');
	}

	public function contacts() {
		return $this->hasMany('App\Models\CounterpartyContact');
	}

    public function contracts() {
	    return $this->hasMany('App\Models\Contract', $this->type_name . '_id', 'id');
    }

    public function trips() {
    	return $this->hasManyThrough('App\Models\Trip', 'App\Models\Contract', $this->type_name . '_id', 'contract_id', 'id', 'id');
    }

    /*
    	SCOPES
    */

	// Заказчики
	public function scopeCustomers($query) {
        return $query->where('counterparty_type_id', self::CUSTOMER);
	}

	// Поставищики
	public function scopeSuppliers($query) {
        return $query->where('counterparty_type_id', self::SUPPLIER);
	}

	// Исполнители
	public function scopeContractors($query) {
        return $query->where('counterparty_type_id', self::CONTRACTOR);
	}

	/*
		ATTRIBUTES
	*/

    public function getTypeNameAttribute() {
    	return ['', 'customer', 'supplier', 'contractor'][(int)$this->counterparty_type_id];
    }

	public function getContractsCountAttribute() {
		return $this->contracts()->count();
    }

    public function getTripsCountAttribute(){
        return $this->trips()->count();
    }

	public $custom_fields = ['contracts_count', 'trips_count'];
}
