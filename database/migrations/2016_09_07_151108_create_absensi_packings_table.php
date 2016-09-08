<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAbsensiPackingsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('absensi_packings', function (Blueprint $table) {
            $table->increments('id');
            $table->date('tanggal');
            $table->string('bagian');
            $table->string('jenis');
            $table->integer('jumlah');
            $table->integer('karyawan_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('absensi_packings');
    }
}
