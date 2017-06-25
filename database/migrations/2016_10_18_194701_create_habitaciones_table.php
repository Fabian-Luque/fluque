<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHabitacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        

        Schema::create('habitaciones', function(Blueprint $table){
        $table->increments('id');
        $table->string('nombre');
        $table->integer('precio_base');
        $table->integer('disponibilidad_base');
        $table->integer('piso');
        $table->integer('propiedad_id')->unsigned();
        $table->foreign('propiedad_id')->references('id')->on('propiedades');
        $table->integer('tipo_habitacion_id')->unsigned()->nullable();
        $table->foreign('tipo_habitacion_id')->references('id')->on('tipo_habitacion');
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
        

        Schema::drop('habitaciones');




    }
}
