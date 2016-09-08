<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatusKaryawansTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('status-karyawans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('keterangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('status-karyawans');
    }
}
