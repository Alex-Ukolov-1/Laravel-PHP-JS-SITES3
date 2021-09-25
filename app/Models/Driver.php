<?php

namespace App\Models;

use App\Models\DriversHistory;
use App\Models\Traits\SecureDelete;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Traits\CRUDFunctions;
use App\Models\Traits\HasPassword;
use App\Models\Traits\FilterByRole;

class Driver extends Authenticatable
{
    use Notifiable;
    use CRUDFunctions;
    use HasPassword;
    use FilterByRole;

    protected $fillable = [
        'organization_id', 'name', 'email', 'password', 'new_password', 'phone', 'status', 'car_id', 'contract_id', 'taxes_in_salary', 'balance', 'is_organization'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public $model_relations = ['trips'];
    public static $model_name = "Водитель";

    protected static function booted()
    {
        parent::booted();

        static::created(function ($driver) {
            if ($driver->car_id) {
                DriversHistory::create(['car_id' => $driver->car_id, 'driver_id' => $driver->id]);
            }
        });

        static::updated(function ($driver) {
            if ($driver->wasChanged('car_id') && $driver->car_id) {
                DriversHistory::create(['car_id' => $driver->car_id, 'driver_id' => $driver->id]);
            }
        });
    }

    public function organization() {
        return $this->belongsTo('App\Models\Organization');
    }

    public function car() {
        return $this->belongsTo('App\Models\Car');
    }

    public function contract() {
        return $this->belongsTo('App\Models\Contract');
    }

    public function trips(){
        return $this->hasMany(Trip::class);
    }

    public function changeCar($car_id) {
        $this->update(['car_id' => $car_id]);
    }

    public function changeContract($contract_id) {
        $this->update(['contract_id' => $contract_id]);
    }
}
