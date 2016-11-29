<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAbsensiHariansTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('absensi_harians', function (Blueprint $table) {
            $table->increments('id');
            $table->date('tanggal');
            $table->integer('karyawan_id');
            $table->time('jam_masuk');
            $table->time('jam_keluar');
            $table->time('jam_lembur');
            $table->integer('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('absensi_harians');
    }
}
