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
    // DASHBOARD VIEW
    Route::get('/dashboard', 'BudgetinQController@dashboard'); // view Dashboard
    Route::get('/dashboard/{time}', 'BudgetinQController@dashboard'); // view Dashboard

    // API
    Route::post('/chartArea', 'BudgetinQController@chartArea'); // return labels, totalPerHari
    Route::post('/chartPie', 'BudgetinQController@chartPie'); // return ? on progress.... 
    // Route::post('/dataGC', 'BudgetinQController@dataGC'); 

    // INPUT DANAMASUK
    Route::get('/danamasuk', 'BudgetinQController@danamasuk'); // on progress.... 
    Route::get('/danamasuk/{time}', 'BudgetinQController@danamasuk'); // on progress.... 
    Route::post('/danamasuk/store', 'PendapatanController@store');  // on progress.... 

    // DANAKELUAR VIEW
    Route::get('/danakeluar', 'BudgetinQController@danakeluar'); // view danakeluar by this month latest
    Route::get('/danakeluar/{time}', 'BudgetinQController@danakeluar'); // view danakeluar by other month
    // Route::get('/danakeluar/{time}/{day}', 'BudgetinQController@danakeluar'); // view danakeluar by day
    Route::get('/danakeluar/kategori/{id_jenis_pengeluaran}', 'BudgetinQController@vDKByK'); // view danakeluar by kategori
    Route::get('/danakeluar/kategori/{id_jenis_pengeluaran}/{time}', 'BudgetinQController@vDKByK'); // view danakeluar by kategori

    // DANAKELUAR CRUD
    Route::post('/danakeluar/store', 'PengeluaranController@store'); // CREATE danakeluar
    Route::post('/danakeluar/edit', 'PengeluaranController@edit'); // EDIT danakeluar
    Route::post('/danakeluar/update', 'PengeluaranController@update'); //UPDATE danakeluar
    Route::post('/danakeluar/delete', 'PengeluaranController@delete'); // DELETE danakeluar
    

    // DANAKELUAR RESPONSE
    Route::get('/dkr', 'BudgetinQController@danakeluarResponse'); // return cPengeluaran, gcPengeluaran, list_pengeluaran 
    Route::get('/dkr/{time}', 'BudgetinQController@danakeluarResponse'); // return cPengeluaran, gcPengeluaran, list_pengeluaran
    Route::get('/dkr/{time}/{day}', 'BudgetinQController@danakeluarResponse'); // return cPengeluaran, gcPengeluaran, list_pengeluaran
    Route::get('/dana/kategori/{id_jenis_pengeluaran}', 'BudgetinQController@danakeluarResponseByKategori'); // return cPengeluaran, gcPengeluaran, list_pengeluaran
    Route::get('/dana/kategori/{id_jenis_pengeluaran}/{time}', 'BudgetinQController@danakeluarResponseByKategori'); // return cPengeluaran, gcPengeluaran, list_pengeluaran

    //CATEGORY DANAKELUAR VIEW
    Route::get('/kategori/danakeluar', 'BudgetinQController@categoryDK'); // view category danakeluar
    Route::get('/kategori/danakeluar/{id_group_kategori}', 'BudgetinQController@categoryDK'); // view category danakeluar
    Route::get('/kategori/danakeluar/{id_group_kategori}/{id_kategori}', 'BudgetinQController@danakeluar'); // view category danakeluar
    Route::get('/kategori/danakeluar/{id_group_kategori}/{id_kategori}/{time}', 'BudgetinQController@categoryDK'); // view category danakeluar
    
    //CATEGORY DANAKELUAR RESPONSE
    Route::get('/kategoriDKR', 'BudgetinQController@categoryDKResponse');
    // Route::get('/kategoriDKR/{time}/{day}', 'BudgetinQController@danakeluarResponse');
    // KategoriDKController

});