<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('clientes', function(Blueprint $table){
        $table->increments('id');
        $table->string('nombre');
        $table->string('apellido');
        $table->string('razon_social');
        $table->string('giro');
        $table->integer('rut');
        $table->string('direccion');
        $table->string('ciudad');
        $table->string('pais');
        $table->string('telefono');
        $table->string('email');
        $table->timestamps();
        $table->softDeletes(); 


        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
         Schema::drop('clientes');


    }
}
