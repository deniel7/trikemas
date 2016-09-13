<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterInvoicePenjualanAddSuratJalan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_penjualans', function (Blueprint $table) {
            $table->dropColumn('angkutan_tujuan_id');
            $table->integer('angkutan_id')->index();
            $table->integer('tujuan_id')->index();
            $table->decimal('harga_angkutan', 15, 2)->nullable();
            $table->string('no_surat_jalan', 20)->nullable();
            $table->string('no_mobil', 20)->nullable()->change();
            $table->string('no_invoice', 20)->nullable()->change();
            $table->string('no_po', 20)->nullable()->change();
            $table->decimal('sub_total', 15, 2)->nullable();
            $table->decimal('diskon', 10, 2)->nullable()->change();
            $table->decimal('total', 15, 2)->nullable()->change();
            $table->decimal('ppn', 10, 2)->nullable()->change();
            $table->decimal('grand_total', 15, 2)->nullable()->change();
            $table->string('created_by');
            $table->string('updated_by')->nullable();
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
            $table->dropColumn('angkutan_id');
            $table->dropColumn('tujuan_id');
            $table->dropColumn('harga_angkutan');
            $table->dropColumn('no_surat_jalan');
            $table->dropColumn('sub_total');
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });
    }
}
