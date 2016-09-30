<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/* Redirect ke halaman login ketika membuka aplikasi */
Route::get('/', function () {
    return redirect('login');
});

// Authentication routes
Route::get('login', 'Auth\AuthController@getLogin');
Route::post('login', 'Auth\AuthController@postLogin');
Route::get('logout', 'Auth\AuthController@getLogout');

// Registration routes (tidak digunakan)
// Route::get('register', 'Auth\AuthController@getRegister');
// Route::post('register', 'Auth\AuthController@postRegister');

Route::group(['middleware' => 'auth'], function () {

    /* Dashboard sebagai halaman pertama setelah login */
    Route::get('home', 'DashboardController@getIndex');

    /* My Account (Update Profile & Password) */
    Route::get('user/profile', 'AuthController@getMyAccount');
    Route::post('user/update-profile', 'AuthController@postUpdateProfile');
    Route::post('user/update-password', 'AuthController@postUpdatePassword');

    /* Provide controller methods with object instead of ID */
    Route::model('karyawan-tetap', 'App\Karyawan');
    Route::model('karyawan-harian', 'App\KaryawanHarian');

    /* Tujuan */
    Route::get('/tujuan/list', 'TujuanController@datatables');
    Route::resource('/tujuan', 'TujuanController');

    /* Angkutan */
    Route::get('/angkutan/list', 'AngkutanController@datatables');
    Route::resource('/angkutan', 'AngkutanController');

    /* Biaya Angkutan */
    Route::get('/angkutan-tujuan/list', 'AngkutanTujuanController@datatables');
    Route::resource('/angkutan-tujuan', 'AngkutanTujuanController');

    /* Barang */
    Route::get('/barang/list', 'BarangController@datatables');
    Route::resource('/barang', 'BarangController');

    /* Konsumen */
    Route::get('/konsumen/list', 'KonsumenController@datatables');
    Route::resource('/konsumen', 'KonsumenController');

    /* Harga Barang */
    Route::get('/konsumen-barang/list', 'KonsumenBarangController@datatables');
    Route::resource('/konsumen-barang', 'KonsumenBarangController');

    /* Datatable */
    Route::post('datatable/karyawans', 'KaryawanTetapController@datatable');
    Route::post('datatable/karyawan-harians', 'KaryawanHarianController@datatable');
    //Route::post('datatable/absensi-harians', 'AbsensiHarianController@datatable');

    /* Select2 */

    /* Master */
        Route::resource('karyawan-tetap', 'KaryawanTetapController');
        Route::controller('karyawan-tetap', 'KaryawanTetapController');
        Route::resource('karyawan-harian', 'KaryawanHarianController');
        Route::controller('karyawan-harian', 'KaryawanHarianController');

    /* Transaction */
    Route::resource('absensi-harian', 'AbsensiHarianController');
    Route::controller('absensi-harian', 'AbsensiHarianController');
    /* Report */

    /* System */
});
