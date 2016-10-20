<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterInvoicePenjualanAddPembayaranAngkutan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_penjualans', function (Blueprint $table) {
            $table->date('tanggal_bayar_angkutan')->nullable();
            $table->decimal('diskon_bayar_angkutan', 15, 2)->nullable();
            $table->decimal('jumlah_bayar_angkutan', 15, 2)->nullable();
            $table->integer('status_bayar_angkutan')->nullable();
            $table->string('bank_tujuan_bayar_angkutan')->nullable();
            $table->string('keterangan_bayar_angkutan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_penjualans', function (Blueprint $table) {
            $table->dropColumn('keterangan_bayar_angkutan');
            $table->dropColumn('bank_tujuan_bayar_angkutan');
            $table->dropColumn('status_bayar_angkutan');
            $table->dropColumn('jumlah_bayar_angkutan');
            $table->dropColumn('diskon_bayar_angkutan');
            $table->dropColumn('tanggal_bayar_angkutan');
        });
    }
}
