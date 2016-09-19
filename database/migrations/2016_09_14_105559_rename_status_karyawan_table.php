<?php

use Illuminate\Database\Migrations\Migration;

class RenameStatusKaryawanTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        $from = 'status-karyawans';
        $to = 'status_karyawans';
        Schema::rename($from, $to);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('status-karyawans');

        Schema::dropIfExists('status-karyawans');
    }
}
