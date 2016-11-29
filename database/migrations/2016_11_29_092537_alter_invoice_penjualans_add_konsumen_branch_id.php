<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterInvoicePenjualansAddKonsumenBranchId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_penjualans', function (Blueprint $table) {
            $table->integer('konsumen_branch_id')->nullable();
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
            $table->dropColumn('konsumen_branch_id');
        });
    }
}
