<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKonsumenBarangsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('konsumen_barangs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('barang_id');
            $table->integer('konsumen_id');
            $table->integer('harga');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('konsumen_barangs');
    }
}
