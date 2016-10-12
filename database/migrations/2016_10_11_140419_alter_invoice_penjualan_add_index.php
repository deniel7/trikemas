<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterInvoicePenjualanAddIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_penjualans', function (Blueprint $table) {
            $table->unique('no_invoice');
            $table->index('no_surat_jalan');
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
            $table->dropIndex('invoice_penjualans_no_surat_jalan_index');
            $table->dropUnique('invoice_penjualans_no_invoice_unique');
        });
    }
}
