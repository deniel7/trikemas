<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDetailPenjualanAddHarga extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('detail_penjualans', function (Blueprint $table) {
            $table->dropColumn('konsumen_barang_id');
            $table->integer('konsumen_id')->index();
            $table->integer('barang_id')->index();
            $table->decimal('harga_barang', 15, 2)->nullable();
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
        Schema::table('detail_penjualans', function (Blueprint $table) {
            $table->dropColumn('konsumen_id');
            $table->dropColumn('barang_id');
            $table->dropColumn('harga_barang');
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });
    }
}
