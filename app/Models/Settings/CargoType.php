<?php

namespace App\Models\Settings;

use App\Models\Settings\SettingsModel;
use App\Models\Traits\FilterByRole;

class CargoType extends SettingsModel
{
	use FilterByRole;

    protected $fillable = [
        'organization_id', 'name', 'default', 'ratio_ton_cubic',  'status'
    ];

    public static $model_name = 'Тип груза';
    public $model_relations = ['contracts', 'trips', 'incomes', 'expenses'];

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

}
