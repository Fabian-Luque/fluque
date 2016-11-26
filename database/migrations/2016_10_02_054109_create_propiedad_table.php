<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropiedadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
            Schema::create('propiedades', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre');
            $table->integer('numero_habitaciones');
            $table->string('ciudad');
            $table->string('direccion');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('tipo_propiedad_id')->unsigned();
            $table->foreign('tipo_propiedad_id')->references('id')->on('tipo_propiedad');
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
        Schema::drop('propiedades');
    }
}
