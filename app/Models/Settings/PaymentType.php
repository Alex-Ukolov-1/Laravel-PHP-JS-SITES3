<?php

namespace App\Models\Settings;

use App\Models\Settings\SettingsModel;
use App\Models\Traits\FilterByRole;

class PaymentType extends SettingsModel
{
	use FilterByRole;

    public static $model_name = 'Форма оплаты';
    public $model_relations = ['unloading_contracts', 'loading_contracts', 'incomes', 'expenses', 'refuels'];

    public function unloading_contracts(){
        return $this->hasMany('App\Models\Contract', 'unloading_payment_type_id', 'id');
    }

    public function loading_contracts(){
        return $this->hasMany('App\Models\Contract', 'loading_payment_type_id', 'id');
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

    static function getDefaultId() {
        $d = self::where('default', 1)->first();
        if ($d) return (int)$d->id;
        else return null;
    }
}
