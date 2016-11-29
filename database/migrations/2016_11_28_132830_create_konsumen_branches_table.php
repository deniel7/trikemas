<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKonsumenBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('konsumen_branches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama', 100);
            $table->integer('konsumen_id');
            $table->string('alamat', 255);
            $table->string('hp', 16);
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('konsumen_branches');
    }
}
