<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMultiusuarioTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create table for storing roles
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->integer('propiedad_id')->unsigned()->nullable();
            $table->foreign('propiedad_id')->references('id')->on('propiedades')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        // Create table for associating roles to users (Many-to-Many)
        Schema::create('role_user', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')
                ->onDelete('cascade');

            $table->primary(['user_id', 'role_id']);
        });

        Schema::create('sections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->timestamps();
            $table->softDeletes();
        });
        
        // Create table for storing permissions
        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->integer('section_id')->unsigned();
            $table->foreign('section_id')->references('id')->on('sections');
            $table->timestamps();
            $table->softDeletes();
        });


        // Create table for associating permissions to roles (Many-to-Many)
        Schema::create('permission_role', function (Blueprint $table) {
            $table->integer('permission_id')->unsigned();
            $table->integer('role_id')->unsigned();
            $table->boolean('status')->default(0);

            $table->foreign('permission_id')->references('id')->on('permissions')
                ->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')
                ->onDelete('cascade');

            $table->primary(['permission_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
