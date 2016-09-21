<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableKaryawansAddTunjangan extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->bigInteger('tunjangan')->nullable();
            $table->bigInteger('nilai_upah')->change();
            $table->bigInteger('uang_makan')->change();
            $table->bigInteger('uang_lembur')->change();
            $table->string('norek', 100)->change();
            $table->string('nik', 100)->change();
            $table->string('phone', 100)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->dropColumn('tunjangan');

        });
    }
}
