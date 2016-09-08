<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTujuansTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tujuans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kota');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('tujuans');
    }
}
