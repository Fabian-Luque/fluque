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
        $table->string('rut')->unique();
        $table->string('direccion');
        $table->string('ciudad');
        $table->string('telefono');
        $table->string('email');
        $table->string('giro')->nullable();
        $table->integer('tipo_cliente_id')->unsigned();
        $table->foreign('tipo_cliente_id')->references('id')->on('tipo_cliente');
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
