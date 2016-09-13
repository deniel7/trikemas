<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableKonsumensAddHp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('konsumens', function (Blueprint $table) {
            $table->string('nama', 100)->change();
            $table->string('hp', 16)->nullable();
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
        Schema::table('konsumens', function (Blueprint $table) {
            $table->dropColumn('hp');
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });
    }
}
