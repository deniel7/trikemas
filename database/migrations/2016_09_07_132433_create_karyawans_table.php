<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKaryawansTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('karyawans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('status_karyawan_id');
            $table->string('bagian');
            $table->integer('nik');
            $table->string('nama');
            $table->string('alamat');
            $table->integer('phone');
            $table->string('lulusan');
            $table->date('tgl_masuk');
            $table->integer('nilai_upah');
            $table->integer('uang_makan');
            $table->integer('uang_lembur');
            $table->integer('pot_koperasi');
            $table->integer('norek');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('karyawans');
    }
}
