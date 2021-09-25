<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Settings\ExpenseCategoryController;
use App\Http\Controllers\Settings\DeparturePointController;
use App\Http\Controllers\Settings\DestinationController;
use App\Http\Controllers\Settings\IntermediatePointController;
use App\Http\Controllers\Settings\StopAndServiceController;
use App\Http\Controllers\Settings\PaymentTypeController;
use App\Http\Controllers\Settings\CargoTypeController;

class SettingsController extends Controller
{
    protected $route = 'settings';

    protected $title = 'Настройки';

    protected $sections = [
        ExpenseCategoryController::class,
        DeparturePointController::class,
        DestinationController::class,
        IntermediatePointController::class,
        StopAndServiceController::class,
        PaymentTypeController::class,
        CargoTypeController::class,
    ];

    public function index()
    {
        $tables = [];

        foreach ($this->sections as $controller) {
            $controller = new $controller;
            $tables[] = $controller->datatable();
        }

        $tables = implode('', $tables);
        return view('crud.index', [
            'content' => $tables,
            'title'   => $this->title
        ]);
    }
}
