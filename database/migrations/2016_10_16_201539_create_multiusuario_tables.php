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
            $table->string('nombre');
            $table->integer('propiedad_id')->unsigned()->nullable();
            $table->foreign('propiedad_id')->references('id')->on('propiedades')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('secciones', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre');
            $table->timestamps();
            $table->softDeletes();
        });
        
        // Create table for storing permissions
        Schema::create('permisos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre');
            $table->integer('seccion_id')->unsigned();
            $table->foreign('seccion_id')->references('id')->on('secciones');
            $table->timestamps();
            $table->softDeletes();
        });


        // Create table for associating permissions to roles (Many-to-Many)
        Schema::create('permiso_rol', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('permiso_id')->unsigned();
            $table->integer('rol_id')->unsigned();
            $table->boolean('estado')->default(0);

            $table->foreign('permiso_id')->references('id')->on('permisos')
                ->onDelete('cascade');
            $table->foreign('rol_id')->references('id')->on('roles')
                ->onDelete('cascade');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('rol_id')->unsigned()->nullable()->after('phone');
            $table->foreign('rol_id')->references('id')->on('roles')->onDelete('set null');
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
