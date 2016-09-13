<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableBarangsModifyBerat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->string('nama', 100)->change();
            $table->string('jenis', 100)->change();
            $table->decimal('berat', 6, 2)->nullable()->change();
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
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });
    }
}
