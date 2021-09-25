<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Traits\CRUDFunctions;
use App\Models\Traits\HasPassword;
use Auth;

class Organization extends Authenticatable
{
    use Notifiable;
    use CRUDFunctions;
    use HasPassword;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'status', 'price_per_km', 'vat_in_fuel_expenses', 'fuel_price', 'average_fuel_consumption'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static $model_name = 'Организация';
    public $model_relations = ['cars', 'drivers', 'contracts', 'trips', 'incomes', 'expenses', 'refuels', 'counterparties', 'expense_categories', 'departure_points'];

    public function cars() {
        return $this->hasMany('App\Models\Car');
    }

    public function drivers() {
        return $this->hasMany('App\Models\Driver');
    }

    public function contracts(){
        return $this->hasMany('App\Models\Contract');
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

    public function counterparties(){
        return $this->hasMany('App\Models\Counterparty');
    }

    public function expense_categories(){
        return $this->hasMany('App\Models\Settings\ExpenseCategory');
    }

    public function departure_points(){
        return $this->hasMany('App\Models\Settings\DeparturePoint');
    }

    public function getUnpaidCars() {
        return $this->cars()->unpaid()->get();
    }
}
