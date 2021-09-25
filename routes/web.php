<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('login');
});

Auth::routes();

Route::post('payments/yookassa_notification', 'Payments\YooPaymentController@yooKassaNotificationHandler')->name('payments.notification_handler');

Route::get('/license', 'InfoController@license')->name('license');
Route::get('/agreement', 'InfoController@agreement')->name('agreement');
Route::get('/privacy', 'InfoController@privacy')->name('privacy');

Route::get('/profit', 'ProfitController@index');
Route::post('/profit', 'ProfitController@setTempDataBeforeRegister')->name('set_temp_data_before_register');

Route::middleware('auth:admin,organization,driver')->group(function () {

    Route::get('/start', 'StartController@index');

    Route::post('profitability/filter', 'ProfitabilityController@getFiltered');
    Route::resource('profitability', 'ProfitabilityController');

    Route::post('trips/filter', 'TripController@getFiltered');
    Route::resource('trips', 'TripController');
    Route::post('report/trip/xls', 'TripController@exportToXls');
    Route::post('report/trip/zip_all', 'TripController@getArchiveAllDocs');
    Route::post('report/trip/email_export_xls', 'TripController@exportToEmailXls');
    Route::post('report/trip/email_export_all', 'TripController@exportToEmailAll');
    Route::get('zip/trip/{id}', 'TripController@getArchiveDocs');

    Route::post('incomes/filter', 'IncomeController@getFiltered');
    Route::resource('incomes', 'IncomeController');

    Route::post('expenses/filter', 'ExpenseController@getFiltered');
    Route::resource('expenses', 'ExpenseController');

    Route::post('refuels/filter', 'RefuelController@getFiltered');
    Route::resource('refuels', 'RefuelController');

    Route::get('login/return_to_admin', 'AuthController@returnToAdmin')->name('auth.return_to_admin');

    Route::post('contracts/filter', 'ContractController@getFiltered');
    Route::resource('contracts', 'ContractController');

    Route::post('departure_points/filter', 'Settings\DeparturePointController@getFiltered');
    Route::get('departure_points/list', 'Settings\DeparturePointController@list');
    Route::resource('departure_points', 'Settings\DeparturePointController');


    Route::post('cargo_types/filter', 'Settings\CargoTypeController@getFiltered');
    Route::get('cargo_types/list', 'Settings\CargoTypeController@list');
    Route::resource('cargo_types', 'Settings\CargoTypeController');


    Route::post('destinations/filter', 'Settings\DestinationController@getFiltered');
    Route::get('destinations/list', 'Settings\DestinationController@list');
    Route::resource('destinations', 'Settings\DestinationController');

    Route::get('expense_categories/list', 'Settings\ExpenseCategoryController@list');
    Route::resource('expense_categories', 'Settings\ExpenseCategoryController')->only(['store']);
});

Route::middleware('auth:admin,organization')->group(function () {

    Route::get('cars/pay', 'Payments\YooPaymentController@pay')->name('cars.pay');
    Route::post('cars/pay', 'Payments\YooPaymentController@pay')->name('cars.pay');
    Route::get('payments/return', 'Payments\YooPaymentController@return')->name('payments.return');
    Route::get('payments/add_card_return',
        'Payments\YooPaymentController@addCardReturn')->name('payments.add_card_return');
    Route::get('payments/{id}/status', 'Payments\YooPaymentController@getPaymentStatus')->name('payments.get_status');

    Route::get('cars/list', 'CarController@list');
    Route::post('cars/filter', 'CarController@getFiltered');
    Route::resource('cars', 'CarController');

    Route::get('drivers/list', 'DriverController@list');
    Route::post('drivers/filter', 'DriverController@getFiltered');
    Route::get('drivers/getCar', 'DriverController@getCar');
    Route::get('drivers/getContract', 'DriverController@getContract');
    Route::resource('drivers', 'DriverController');

    Route::post('counterparties/filter', 'CounterpartyController@getFiltered');
    Route::resource('counterparties', 'CounterpartyController');

    Route::post('transactions/filter', 'TransactionController@getFiltered');
    Route::resource('transactions', 'TransactionController');

    Route::get('customers/list', 'CustomerController@list');
    Route::post('customers/filter', 'CustomerController@getFiltered');
    Route::resource('customers', 'CustomerController');

    Route::get('suppliers/list', 'SupplierController@list');
    Route::post('suppliers/filter', 'SupplierController@getFiltered');
    Route::resource('suppliers', 'SupplierController');

    Route::get('contractors/list', 'ContractorController@list');
    Route::post('contractors/filter', 'ContractorController@getFiltered');
    Route::resource('contractors', 'ContractorController');

    Route::get('contract_from_profitability/{profitability_id}',
        'ContractController@create_from_profitability')->name('contract_from_profitability');
    Route::get('contract_without_profitability',
        'ContractController@create_without_profitability')->name('create_without_profitability');

    Route::get('settings', 'SettingsController@index');

    Route::resource('payments', 'PaymentController');

    /* Settings */
    Route::post('expense_categories/filter', 'Settings\ExpenseCategoryController@getFiltered');
    Route::resource('expense_categories', 'Settings\ExpenseCategoryController')->except(['store']);

    Route::post('payment_types/filter', 'Settings\PaymentTypeController@getFiltered');
    Route::resource('payment_types', 'Settings\PaymentTypeController');


    Route::post('intermediate_points/filter', 'Settings\IntermediatePointController@getFiltered');
    Route::resource('intermediate_points', 'Settings\IntermediatePointController');

    Route::post('stops_and_services/filter', 'Settings\StopAndServiceController@getFiltered');
    Route::resource('stops_and_services', 'Settings\StopAndServiceController');

    Route::get('unit_types/list', 'Settings\UnitTypeController@list');
    Route::post('unit_types/filter', 'Settings\UnitTypeController@getFiltered');
    Route::resource('unit_types', 'Settings\UnitTypeController');
    /* /Settings */

    Route::post('organizations/filter', 'OrganizationController@getFiltered');
    Route::resource('organizations', 'OrganizationController');

});

Route::middleware('auth:admin')->group(function () {
    /*
    Route::post('organizations/filter', 'OrganizationController@getFiltered');
    Route::resource('organizations', 'OrganizationController');
    */

    Route::post('yoo_payments/filter', 'YooPaymentController@getFiltered');
    Route::get('yoo_payments', 'YooPaymentController@index');

    Route::post('car_payment_history/filter', 'CarPaymentHistoryController@getFiltered');
    Route::get('car_payment_history', 'CarPaymentHistoryController@index');

    Route::get('drivers/login/{id}', 'AuthController@loginAsDriver')->name('auth.login_as_driver');
    Route::get('organizations/login/{id}', 'AuthController@loginAsOrganization')->name('auth.login_as_organization');
});
