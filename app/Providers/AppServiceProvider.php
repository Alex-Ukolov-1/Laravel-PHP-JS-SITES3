<?php

namespace App\Providers;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Organization;
use App\Observers\ExpenseObserver;
use App\Observers\IncomeObserver;
use App\Observers\OrganizationObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Organization::observe(OrganizationObserver::class);
        Income::observe(IncomeObserver::class);
        Expense::observe(ExpenseObserver::class);
    }
}
