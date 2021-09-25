<?php

namespace App\Models\Settings;

use App\Models\Settings\SettingsModel;
use App\Models\Traits\FilterByRole;

class DeparturePoint extends SettingsModel
{
	use FilterByRole;

    public static $model_name = 'Пункты отправления';
    public $model_relations = ['contracts', 'trips'];

    public function contracts(){
        return $this->hasMany('App\Models\Contract');
    }

    public function trips(){
        return $this->hasMany('App\Models\Trip');
    }

}
