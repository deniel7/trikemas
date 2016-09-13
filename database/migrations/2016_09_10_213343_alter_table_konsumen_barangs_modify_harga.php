<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableKonsumenBarangsModifyHarga extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('konsumen_barangs', function (Blueprint $table) {
            $table->decimal('harga', 15, 2)->nullable()->change();
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->index('barang_id');
            $table->index('konsumen_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('konsumen_barangs', function (Blueprint $table) {
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });
    }
}
