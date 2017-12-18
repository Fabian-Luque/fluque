<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMotorPropiedadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('motor_propiedad', function(Blueprint $table){
            $table->increments('id');
            $table->integer('propiedad_id')->unsigned();
            $table->foreign('propiedad_id')->references('id')->on('propiedades');
            $table->integer('color_motor_id')->unsigned();
            $table->foreign('color_motor_id')->references('id')->on('colores_motor');
            $table->integer('clasificacion_color_id')->unsigned();
            $table->foreign('clasificacion_color_id')->references('id')->on('clasificacion_color');
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
        //
    }
}
