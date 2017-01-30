var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix.sass('app.scss');
});

elixir(function(mix) {
    mix.scripts([
        'common.js',
        'karyawan.js',
        'karyawanHarian.js',
        'karyawanStaff.js',
        'absensiHarian.js',
        'absensiApproval.js',
        'absensiPacking.js',
        'karyawanValidation.js',
        'reportAbsensiKaryawanTetap.js',
        'reportAbsensiKaryawanHarian.js',
        'reportAbsensiKaryawanPacking.js',
        'reportAbsensiKaryawanStaff.js',
        'upahJenisBarang.js',
    ]);
});