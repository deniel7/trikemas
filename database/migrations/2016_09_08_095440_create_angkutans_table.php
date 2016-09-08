<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAngkutansTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('angkutans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('angkutans');
    }
}
