<?php

use Illuminate\Database\Migrations\Migration;

class CreateReportJenisTable extends Migration
{
    public function up()
    {
        Schema::create('report_jenis', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama');
            $table->bigInteger('upah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('report_jenis');
    }
}
