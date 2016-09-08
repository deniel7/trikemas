<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePembayaranAngkutansTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('pembayaran_angkutans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_penjualan_id');
            $table->integer('angkutan_tujuan_id');
            $table->date('tanggal');
            $table->integer('diskon');
            $table->integer('jumlah');
            $table->string('keterangan');
            $table->integer('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('pembayaran_angkutans');
    }
}
