<?php

namespace App\Models\Payments;

use Illuminate\Database\Eloquent\Model;
use App\Models\Car;

abstract class Payment extends Model
{
	protected $casts = [
	  'cars' => 'array',
	  'detail' => 'array',
	];

	protected $attributes = [
		'cars' => '[]',
		'months' => 1,
		'amount' => 0,
	];

	private $price_per_car;
	private $_detail;
	private $added_cars = [];

	public function __construct(array $attributes = []) {
		parent::__construct($attributes);
		
		$this->price_per_car = env('PRICE_PER_CAR');
		
		if (empty($this->detail)) {
			$this->detail = [
				'cars' => [],
				'type' => 'payment',
				'price_per_car' => $this->price_per_car,
				'period' => $this->months,
				'total' => $this->amount,
			];
		}

		$this->_detail = $this->detail;
	}

	private function addCar($car) {
		if (!$car instanceof Car) {
			$car = Car::find($car);
			if ($car === null) return;
		}

		$this->cars = array_merge($this->cars, [$car->id]);
		$this->added_cars[] = $car;

		$price = $this->price_per_car;

		$paid = $price * $this->months;

		$this->amount += $paid;

		if ((int)$car->is_paid === false) { // Оплата или доплата
		  $paid_before = date('Y-m-d', strtotime(' +' . $this->months . ' month'));
		} else { // Продление
		  $paid_before = date('Y-m-d', strtotime($car->paid_before . ' +' . $this->months . ' month'));
		}

		$this->_detail['cars'][] = [
		  'id' => $car->id,
		  'number' => $car->number,
		  'price' => $price,
		  'paid' => $paid,
		  'paid_before' => $paid_before,
		];

		$this->_detail['total'] = $this->amount;
		$this->detail = $this->_detail;
	}

	public function addCars($cars) {
		foreach ($cars as $car) {
			$this->addCar($car);
		}
	}

	private function recalc() {
		$this->_detail['cars'] = [];
		$this->_detail['total'] = 0;

		$this->detail = $this->_detail;

		$cars = $this->added_cars;
		$this->added_cars = [];
		$this->cars = [];
		$this->amount = 0;

		$this->addCars($cars);
	}

	public function setPeriod($months) {
		$this->months = $months;

		$this->_detail['period'] = $months;
		$this->detail = $this->_detail;

		$this->recalc();
	}

	public function setStatus($status) {
		$this->status = $status;
	}

	public function numberOfCars() {
		return count($this->detail['cars']);
	}

	public function hasCars() {
		return $this->numberOfCars() > 0;
	}

	public function getPricePerCarAttribute() {
		return (float)$this->detail['price_per_car'];
	}

	public function activateCars() {
		foreach ($this->cars_list as $car) {
			$car->pay($this);
		}
	}

	public function deactivateCars() {
		foreach ($this->cars_list as $car) {
			$car->deactivate();
		}
	}

	public function resetCars() {
		foreach ($this->cars_list as $car) {
			$car->reset();
		}
	}

	public function organization() {
		return $this->belongsTo('App\Models\Organization');
	}

	public function getStatusHrAttribute() {
		return $this::STATUSES[$this->status] ?? '';
	}

	public function getCarsListAttribute() {
		return Car::whereIn('id', $this->cars)->get();
	}

	public function getDetailHrAttribute() {
	  if (empty($this->detail) || !isset($this->detail['type'])) return '';

	  $detail = $this->detail;

	  if ($detail['type'] === 'card') return 'Подключение карты';

	  $result = '';

	  $cars = $detail['cars'] ?? [];
	  $period = $detail['period'] ?? null;
	  $price_per_car = $detail['price_per_car'] ?? null;

    foreach ($cars as $car) {
      $result .= ($car['number'] ?? '') . " (" . ($car['price'] ?? $price_per_car ?? '') . "р. x {$period} мес.) до " . $car['paid_before'] . PHP_EOL;
    }

	  return trim($result);
	}
}
