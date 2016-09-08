<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetailPenjualansTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('detail_penjualans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_penjualan_id');
            $table->integer('konsumen_barang_id');
            $table->integer('jumlah');
            $table->integer('subtotal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('detail_penjualans');
    }
}
