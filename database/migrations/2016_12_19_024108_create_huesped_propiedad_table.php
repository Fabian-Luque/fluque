<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHuespedPropiedadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('huesped_propiedad', function (Blueprint $table) {
            $table->increments('id');
            $table->string('comentario',250);
            $table->integer('calificacion')->unsigned();
            $table->integer('huesped_id')->unsigned();
            $table->foreign('huesped_id')->references('id')->on('huespedes');
            $table->integer('propiedad_id')->unsigned();
            $table->foreign('propiedad_id')->references('id')->on('propiedades');
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
        Schema::drop('huesped_propiedad');
    }
}
