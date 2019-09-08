<?php

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
date_default_timezone_set('Asia/Jakarta');

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::get('auth/{provider}','Auth\LoginController@redirectToProvider');
Route::get('auth/{provider}/callback','Auth\LoginController@handleProviderCallback');
Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

Route::group(['middleware' => ['auth']], function() {
    // DASHBOARD
    Route::get('/dashboard', 'BudgetinQController@dashboard');
    Route::get('/dashboard/{time}', 'BudgetinQController@dashboard');
    Route::post('/chartArea', 'BudgetinQController@chartArea'); 
    Route::post('/chartPie', 'BudgetinQController@chartPie'); 

    // INPUT DANAMASUK
    Route::get('/danamasuk', 'BudgetinQController@danamasuk');
    Route::get('/danamasuk/{time}', 'BudgetinQController@danamasuk');
    Route::post('/dataGC', 'BudgetinQController@dataGC'); 
    

    // DANAKELUAR
    Route::get('/danakeluar', 'BudgetinQController@danakeluar');
    Route::get('/danakeluar/{time}', 'BudgetinQController@danakeluar');
});