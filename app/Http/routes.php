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

     // laporan
    Route::get('/report', 'ReportController@index');
    Route::get('/report/penjualan', 'ReportController@penjualan');
    Route::get('/report/penjualan/preview/{ppn}/{dari}/{hingga?}', 'ReportController@previewPenjualan');

    Route::get('/report/absensi-karyawan-staff', 'ReportController@absensiKaryawanStaff');
    Route::get('/report/absensi-karyawan-staff/preview/{bulan}', 'ReportController@previewAbsensiKaryawanStaff');

    Route::get('/report/absensi-karyawan-tetap', 'ReportController@absensiKaryawanTetap');
    Route::get('/report/absensi-karyawan-tetap/preview/{bulan}', 'ReportController@previewAbsensiKaryawanTetap');

    Route::get('/report/absensi-karyawan-harian', 'ReportController@absensiKaryawanHarian');
    Route::get('/report/absensi-karyawan-harian/preview/{dari}/{hingga?}', 'ReportController@previewAbsensiKaryawanHarian');

    Route::get('/report/absensi-karyawan-packing', 'ReportController@absensiKaryawanPacking');
    Route::get('/report/absensi-karyawan-packing/preview/{dari}/{hingga?}', 'ReportController@previewAbsensiKaryawanPacking');

    /* Dashboard sebagai halaman pertama setelah login */
    Route::get('home', 'DashboardController@getIndex');

    /* My Account (Update Profile & Password) */
    Route::get('user/profile', 'AuthController@getMyAccount');
    Route::post('user/update-profile', 'AuthController@postUpdateProfile');
    Route::post('user/update-password', 'AuthController@postUpdatePassword');

    /* Provide controller methods with object instead of ID */
    Route::model('karyawan-tetap', 'App\Karyawan');
    Route::model('karyawan-harian', 'App\Karyawan');
    Route::model('karyawan-staff', 'App\Karyawan');

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
    Route::get('/barang/autocomplete', 'BarangController@autocomplete');
    Route::resource('/barang', 'BarangController');

    /* Konsumen */
    Route::get('/konsumen/list', 'KonsumenController@datatables');
    Route::get('/konsumen/branch/{id}', 'KonsumenController@branch');
    Route::resource('/konsumen', 'KonsumenController');

    /* Konsumen Branch */
    Route::get('/konsumen-branch/list', 'KonsumenBranchController@datatables');
    Route::resource('/konsumen-branch', 'KonsumenBranchController');

    /* Harga Barang */
    Route::get('/konsumen-barang/list', 'KonsumenBarangController@datatables');
    Route::get('/konsumen-barang/get-price-by-id/{item_id}/{konsumen_id}', 'KonsumenBarangController@getPriceById');
    Route::get('/konsumen-barang/get-price/{item_name}/{konsumen_id}', 'KonsumenBarangController@getPrice');
    Route::resource('/konsumen-barang', 'KonsumenBarangController');

    /* Invoice Penjualan */
    Route::post('/invoice/list', 'InvoiceController@datatables');
    Route::get('/invoice/print/{id}', 'InvoiceController@doPrint');
    Route::post('/invoice/complete/{id}', 'InvoiceController@complete');
    Route::resource('/invoice', 'InvoiceController');

    /* Pembayaran Angkutan */
    Route::get('/pembayaran-angkutan/list', 'PembayaranAngkutanController@datatables');
    Route::post('/pembayaran-angkutan/complete/{id}', 'PembayaranAngkutanController@complete');
    Route::resource('/pembayaran-angkutan', 'PembayaranAngkutanController');

    /* Datatable */
    Route::post('datatable/karyawan-staff', 'KaryawanStaffController@datatable');
    Route::post('datatable/karyawans', 'KaryawanTetapController@datatable');
    Route::post('datatable/karyawan-harians', 'KaryawanHarianController@datatable');
    Route::post('datatable/absensi-harians', 'AbsensiHarianController@datatable');
    Route::post('datatable/absensi-approvals', 'AbsensiApprovalController@datatable');
    Route::post('datatable/absensi-packings', 'AbsensiPackingController@datatable');

    /* karyawan */
    Route::post('karyawan-staff/print/', 'KaryawanStaffController@doPrint');
    Route::resource('karyawan-staff', 'KaryawanStaffController');
    Route::controller('karyawan-staff', 'KaryawanStaffController');

    Route::post('karyawan-tetap/print/', 'KaryawanTetapController@doPrint');
    Route::resource('karyawan-tetap', 'KaryawanTetapController');
    Route::controller('karyawan-tetap', 'KaryawanTetapController');

    Route::post('karyawan-harian/print/', 'KaryawanHarianController@doPrint');
    Route::resource('karyawan-harian', 'KaryawanHarianController');
    Route::controller('karyawan-harian', 'KaryawanHarianController');

    /* Absensi */

    Route::resource('absensi-harian', 'AbsensiHarianController');
    Route::controller('absensi-harian', 'AbsensiHarianController');
    Route::resource('absensi-packing', 'AbsensiPackingController');
    Route::controller('absensi-packing', 'AbsensiPackingController');
    Route::post('absensi-approval/potongan/{id}', 'AbsensiApprovalController@postUpdate');
    Route::resource('absensi-approval', 'AbsensiApprovalController');

    /* upload jam lembur */
    Route::post('upload-absen/lembur/{id}', 'UploadAbsenController@postUpdate');
    Route::resource('upload-absen', 'UploadAbsenController');
    Route::controller('upload-absen', 'UploadAbsenController');

});
