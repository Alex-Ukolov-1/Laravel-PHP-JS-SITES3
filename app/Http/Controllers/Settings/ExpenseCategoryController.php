<?php

namespace App\Http\Controllers\Settings;

use App\Models\Settings\ExpenseCategory;
use App\Http\Controllers\Settings\SettingsController;

class ExpenseCategoryController extends SettingsController
{
    protected $model = ExpenseCategory::class;

    protected $route = 'expense_categories';

    protected $title = 'Категории расходов';
}
