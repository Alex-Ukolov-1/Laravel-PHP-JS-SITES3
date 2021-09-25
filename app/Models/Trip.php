<?php

namespace App\Models;

use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\CRUDFunctions;
use Auth;
use App\Models\Traits\FilterByRole;
use App\Http\Controllers\ProfitabilityController;

class Trip extends Model
{
	use CRUDFunctions;
	use FilterByRole;

	protected $fillable = [
	  'organization_id', 'date', 'contract_id', 'car_id', 'driver_id', 'cargo_type_id', 'departure_point_id', 'destination_id', 'loading_cargo_amount', 'unloading_cargo_amount',
	  'loading_unit_type_id', 'unloading_unit_type_id', 'comment', 'total_driver_salary',
	];

	public $timestamps = false;

	public $model_relations = ['documents'];

	public static $model_name = "Рейс";

	private $_profitability;

	protected static function booted()
	{
        static::created(function ($trip) {
            if ($trip->driver_id) {
                (new TransactionService())
                    ->setDriver($trip->driver_id)
                    ->setOrganization($trip->organization_id)
                    ->setType(Transaction::TYPE_TRIP)
                    ->setActionID($trip->id)
                    ->setActionBalance($trip->total_driver_salary)
                    ->setDescription($trip->cargo_type->name)
                    ->create();
            }
        });

        static::updating(function ($trip) {
            if ($trip->isDirty('total_driver_salary')) {
                (new TransactionService())->changeDriverSalaryInTrip($trip);
            }
        });
	}

	public function contract() {
	    return $this->belongsTo('App\Models\Contract');
	}

	public function car() {
	    return $this->belongsTo('App\Models\Car')->withTrashed();
	}

	public function driver() {
	    return $this->belongsTo('App\Models\Driver');
	}

	public function organization() {
	    return $this->belongsTo('App\Models\Organization');
	}

	public function departure_point() {
	    return $this->belongsTo('App\Models\Settings\DeparturePoint');
	}

	public function destination() {
	    return $this->belongsTo('App\Models\Settings\Destination')->withTrashed();
	}

	public function intermediate_point() {
	    return $this->belongsToMany('App\Models\Settings\IntermediatePoint', 'trip_intermediate_point', 'trip_id', 'intermediate_point_id');
	}

	public function stop_and_service() {
	    return $this->belongsToMany('App\Models\Settings\StopAndService', 'trip_stop_and_service', 'trip_id', 'stop_and_service_id');
	}

	public function cargo_type() {
	    return $this->belongsTo('App\Models\Settings\CargoType');
	}

	public function loading_unit_type() {
	    return $this->belongsTo('App\Models\Settings\UnitType');
	}

	public function unloading_unit_type() {
	    return $this->belongsTo('App\Models\Settings\UnitType');
	}

	public function documents(){
	    return $this->hasMany('App\Models\TripDocument');
	}

	public function getIntermediatePointNamesAttribute() {
		return $this->intermediate_point->implode('name', ', ');
	}

	public function getStopAndServiceNamesAttribute() {
		return $this->stop_and_service->implode('name', ', ');
	}

	public function getDateOnlyAttribute() {
		return explode(' ', $this->date)[0];
	}

	public function getProfitability() {
		if (empty($this->_profitability)) {
			try {
				$profitability_controller = new ProfitabilityController;

				$data = array_merge($this->contract->toArray(), $this->setAppends([])->toArray());

				$data['unloading_volume'] = $data['unloading_cargo_amount'];
				$data['loading_volume'] = $data['loading_cargo_amount'];
				$data['unloading_volume_type_id'] = $data['unloading_unit_type_id'];
				$data['unloading_price_type_id'] = $data['unloading_unit_type_id'];
				$data['loading_volume_type_id'] = $data['loading_unit_type_id'];
				$data['loading_price_type_id'] = $data['loading_unit_type_id'];
				$data['driver_salary_direction_id'] = $data['trip_direction_id'];
				$data['price_of_fuel'] = (string)$this->organization->fuel_price;
				$data['average_fuel_consumption'] = (string)$this->organization->average_fuel_consumption;
				$data['fixed_overhead'] = 0;
				$data['additional_overhead'] = 0;
				$data['with_taxes'] = (string)$this->driver->taxes_in_salary;
				$data['vat_in_fixed_overhead'] = '0';
				$data['vat_in_additional_overhead'] = '0';
				$data['vat_in_fuel_expenses'] = (string)$this->organization->vat_in_fuel_expenses;

				$this->_profitability = $profitability_controller->calc($data);
			} catch (\Throwable $e) {
				$this->_profitability = array(
					'fullTripDistance' => 0,
					'revenueForTheTripWithoutVAT' => 0,
					'costOfTheCargoWithoutVAT' => 0,
					'driversSalaryForTheTripWithoutVAT' => 0,
					'priceOfFuelForTheTripWithoutVAT' => 0,
					'fixedOverheadWithoutVAT' => 0,
					'additionalOverheadWithoutVAT' => 0,
					'shippingCost' => 0,
					'profit' => 0,
				);
			}
		}

		return $this->_profitability;
	}

	public function getRevenueAttribute() {
		return $this->getProfitability()['revenueForTheTripWithoutVAT'];
    }

    public function getProfitAttribute() {
    	return $this->getProfitability()['profit'];
    }

    public function calcDriverSalary() {
    	return $this->getProfitability()['driversSalaryForTheTripWithoutVAT'];
    }

    public function getDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }

	public $manyToManyRelations = ['intermediate_point', 'stop_and_service'];

	public $custom_fields = ['revenue', 'profit'];
}
