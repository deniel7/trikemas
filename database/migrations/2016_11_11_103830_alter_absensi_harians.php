<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAbsensiHarians extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('absensi_harians', function (Blueprint $table) {

            $table->string('jam_kerja');
            $table->renameColumn('jam_keluar', 'jam_pulang');
            $table->time('scan_masuk');
            $table->time('scan_pulang');
            $table->time('terlambat')->nullable();
            $table->time('plg_cepat')->nullable();
            $table->time('jml_jam_kerja');
            $table->string('departemen');
            $table->time('jml_kehadiran');
            $table->integer('jam')->nullable();
            $table->integer('menit')->nullable();
            $table->integer('konfirmasi_lembur')->nullable();
            $table->bigInteger('pot_absensi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('absensi_harians', function (Blueprint $table) {
            $table->dropColumn('jam_kerja');
            $table->dropColumn('jam_pulang');
            $table->dropColumn('scan_masuk');
            $table->dropColumn('scan_pulang');
            $table->dropColumn('terlambat');
            $table->dropColumn('plg_cepat');
            $table->dropColumn('jml_jam_kerja');
            $table->dropColumn('departemen');
            $table->dropColumn('jml_kehadiran');
            $table->dropColumn('jam');
            $table->dropColumn('menit');
        });
    }
}
