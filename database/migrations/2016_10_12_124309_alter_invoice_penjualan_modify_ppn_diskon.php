<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterInvoicePenjualanModifyPpnDiskon extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_penjualans', function (Blueprint $table) {
            $table->decimal('ppn', 15, 2)->nullable()->change();
            $table->decimal('diskon', 15, 2)->nullable()->change();
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
            //
        });
    }
}
