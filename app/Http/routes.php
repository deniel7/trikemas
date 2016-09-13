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

    /* Datatable */

    /* Select2 */

    /* Master */
        Route::resource('karyawan-tetap', 'KaryawanTetapController');

    /* Transaction */

    /* Report */

    /* System */
    });
