<?php

namespace App\Models\Settings;

use App\Models\Settings\SettingsModel;
use App\Models\Traits\FilterByRole;

class ExpenseCategory extends SettingsModel
{
	use FilterByRole;

    public static $model_name = 'Категории расходов';
    public $model_relations = ['expenses'];


    public function expenses(){
        return $this->hasMany('App\Models\Expense');
    }
}
