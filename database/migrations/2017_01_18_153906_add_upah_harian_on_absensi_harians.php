<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUpahHarianOnAbsensiHarians extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('absensi_harians', function (Blueprint $table) {
            $table->bigInteger('upah_harian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('absensi_harians', function (Blueprint $table) {
            $table->dropColumn('upah_harian');
        });
    }
}
