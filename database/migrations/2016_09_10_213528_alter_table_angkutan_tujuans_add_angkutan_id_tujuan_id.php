<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAngkutanTujuansAddAngkutanIdTujuanId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('angkutan_tujuans', function (Blueprint $table) {
            $table->integer('angkutan_id')->index();
            $table->integer('tujuan_id')->index();
            $table->decimal('harga', 15, 2);
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
        Schema::table('angkutan_tujuans', function (Blueprint $table) {
            $table->dropColumn('angkutan_id');
            $table->dropColumn('tujuan_id');
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });
    }
}
