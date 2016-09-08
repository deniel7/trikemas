<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicePenjualansTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('invoice_penjualans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('konsumen_id');
            $table->integer('angkutan_tujuan_id');
            $table->date('tanggal');
            $table->integer('no_mobil');
            $table->integer('no_invoice');
            $table->integer('no_po');
            $table->date('tgl_jatuh_tempo');
            $table->integer('diskon');
            $table->integer('total');
            $table->integer('ppn');
            $table->integer('grand_total');
            $table->string('bank_tujuan_bayar');
            $table->date('tanggal_bayar');
            $table->integer('status_bayar');
            $table->string('keterangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('invoice_penjualans');
    }
}
