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
    Route::post('/dashboardResponse', 'BudgetinQController@dashboardResponse'); 

    // DANAMASUK VIEW
    Route::get('/danamasuk', 'BudgetinQController@danamasuk'); // view danamasuk by this month latest
    Route::get('/danamasuk/{time}', 'BudgetinQController@danamasuk'); // on progress.... 

    Route::get('/danamasuk/kategori/{id_jenis_pengeluaran}', 'BudgetinQController@vDMByK'); // view danamasuk by kategori
    Route::get('/danamasuk/kategori/{id_jenis_pengeluaran}/{time}', 'BudgetinQController@vDMByK'); // view danamasuk by kategori
    Route::get('/danamasuk/{time}/{day}', 'BudgetinQController@danamasuk'); // view danamasuk by day

    // DANAKELUAR CRUD
    Route::post('/danamasuk/store', 'PendapatanController@store'); // CREATE danamasuk
    Route::post('/danamasuk/edit', 'PendapatanController@edit'); // EDIT danamasuk
    Route::post('/danamasuk/update', 'PendapatanController@update'); //UPDATE danamasuk
    Route::post('/danamasuk/delete', 'PendapatanController@delete'); // DELETE danamasuk
    

    // DANAMASUK RESPONSE
Route::get('/dmr', 'BudgetinQController@danamasukResponse'); // return cPendapatanta, gcPendapatanta, list_pendapatan 
    Route::get('/dmr/{time}', 'BudgetinQController@danamasukResponse'); // return cPendapatanta, gcPendapatanta, list_pendapatan
    Route::get('/dmr/{time}/{day}', 'BudgetinQController@danamasukResponse'); // return cPendapatanta, gcPendapatanta, list_pendapatan
    Route::get('/danaDM/kategori/{id_jenis_pengeluaran}', 'BudgetinQController@danamasukResponseByKategori'); // return cPendapatanta, gcPendapatanta, list_pendapatan
    Route::get('/danaDM/kategori/{id_jenis_pengeluaran}/{time}', 'BudgetinQController@danamasukResponseByKategori'); // return cPendapatanta, gcPendapatanta, list_pendapatan


    // DANAKELUAR VIEW
    Route::get('/danakeluar', 'BudgetinQController@danakeluar'); // view danakeluar by this month latest
    Route::get('/danakeluar/{time}', 'BudgetinQController@danakeluar'); // view danakeluar by other month
    Route::get('/danakeluar/{time}/{day}', 'BudgetinQController@danakeluar'); // view danakeluar by day
    
    Route::get('/danakeluar/kategori/{id_jenis_pengeluaran}', 'BudgetinQController@danakeluarByKategori'); // view danakeluar by kategori
    Route::get('/danakeluar/kategori/{id_jenis_pengeluaran}/{time}', 'BudgetinQController@danakeluarByKategori'); // view danakeluar by kategori
    

    // DANAKELUAR CRUD
    Route::post('/danakeluar/store', 'PengeluaranController@store'); // CREATE danakeluar
    Route::post('/danakeluar/edit', 'PengeluaranController@edit'); // EDIT danakeluar
    Route::post('/danakeluar/update', 'PengeluaranController@update'); //UPDATE danakeluar
    Route::post('/danakeluar/delete', 'PengeluaranController@delete'); // DELETE danakeluar
    Route::get('/generatePengeluaranPDF/{time}', 'PengeluaranController@generatePDF');

    // DANAKELUAR RESPONSE
    Route::get('/dkr', 'BudgetinQController@danakeluarResponse'); // return cPengeluaran, gcPengeluaran, list_pengeluaran 
    Route::get('/dkr/{time}', 'BudgetinQController@danakeluarResponse'); // return cPengeluaran, gcPengeluaran, list_pengeluaran
    Route::get('/dkr/{time}/{day}', 'BudgetinQController@danakeluarResponse'); // return cPengeluaran, gcPengeluaran, list_pengeluaran
    Route::get('/danaDK/kategori/{id_jenis_pengeluaran}', 'BudgetinQController@danakeluarResponseByKategori'); // return cPengeluaran, gcPengeluaran, list_pengeluaran
    Route::get('/danaDK/kategori/{id_jenis_pengeluaran}/{time}', 'BudgetinQController@danakeluarResponseByKategori'); // return cPengeluaran, gcPengeluaran, list_pengeluaran

    //KATEGORI DANAKELUAR VIEW
    Route::get('/kategori/danakeluar', 'BudgetinQController@kategoriDK'); // view category danakeluar all periode
    Route::get('/kategori/danakeluar/{time}', 'BudgetinQController@kategoriDK'); // view category danakeluar per bulan
    // Route::get('/kategori/danakeluar/{id_group_kategori}/{id_kategori}', 'BudgetinQController@danakeluar'); // view category danakeluar
    // Route::get('/kategori/danakeluar/{id_group_kategori}/{id_kategori}/{time}', 'BudgetinQController@kategoriDK'); // view category danakeluar
    
    //KATEGORI DANAKELUAR RESPONSE
    Route::get('/kategoriDKR', 'BudgetinQController@kategoriDKResponse');
    Route::get('/kategoriDKR/{time}', 'BudgetinQController@kategoriDKResponse');
    
    // KATEGORI DANAKELUAR CRUD
    Route::post('/kategori/danakeluar/store', 'JenisPengeluaranController@store'); // CREATE danakeluar
    Route::post('/kategori/danakeluar/edit', 'JenisPengeluaranController@edit'); // EDIT danakeluar
    Route::post('/kategori/danakeluar/update', 'JenisPengeluaranController@update'); //UPDATE danakeluar
    Route::post('/kategori/danakeluar/delete', 'JenisPengeluaranController@delete'); // DELETE danakeluar
    
    //KATEGORI DANAMASUK VIEW
    Route::get('/kategori/danamasuk', 'BudgetinQController@categoryDM'); // view category danamasuk all periode
    Route::get('/kategori/danamasuk/{time}', 'BudgetinQController@categoryDM'); // view category danamasuk per bulan
    // Route::get('/kategori/danamasuk/{id_group_kategori}/{id_kategori}', 'BudgetinQController@danamasuk'); // view category danamasuk
    // Route::get('/kategori/danamasuk/{id_group_kategori}/{id_kategori}/{time}', 'BudgetinQController@categoryDM'); // view category danamasuk
    
    //KATEGORI DANAMASUK RESPONSE
    Route::get('/kategoriDMR', 'BudgetinQController@categoryDMResponse');
    Route::get('/kategoriDMR/{time}', 'BudgetinQController@categoryDMResponse');
    
    // KATEGORI DANAMASUK CRUD
    Route::post('/kategori/danamasuk/store', 'JenisPendapatanController@store'); // CREATE danamasuk
    Route::post('/kategori/danamasuk/edit', 'JenisPendapatanController@edit'); // EDIT danamasuk
    Route::post('/kategori/danamasuk/update', 'JenisPendapatanController@update'); //UPDATE danamasuk
    Route::post('/kategori/danamasuk/delete', 'JenisPendapatanController@delete'); // DELETE danamasuk
    



});