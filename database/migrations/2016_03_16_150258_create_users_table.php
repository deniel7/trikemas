<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('full_name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->integer('role_id');
            $table->string('password', 60);
            $table->rememberToken();
            $table->tinyInteger('is_suspended')->default(0);
            $table->tinyInteger('is_disabled')->default(0);
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();

            // $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('users');
    }
}
