<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSysUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_user', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('username', 20)->unique();
            $table->string('password');
            $table->string('name', 100);
            $table->string('email', 100)->nullable();
            $table->rememberToken();
            $table->string('rolename', 20)->nullable()->index();
            $table->integer('active')->default(0);
            $table->string('created_by', 20);
            $table->string('updated_by', 20)->nullable();
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
        Schema::drop('sys_user');
    }
}
