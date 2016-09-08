<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionRoleTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('permission_role', function (Blueprint $table) {
            $table->integer('permission_id');
            $table->integer('role_id');
            $table->timestamps();

            // $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            // $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');

            $table->primary(['permission_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('permission_role');
    }
}
