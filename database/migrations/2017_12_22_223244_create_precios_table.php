<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePreciosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('precios', function(Blueprint $table){
        $table->increments('id');
        $table->integer('precio_habitacion');
        $table->integer('habitacion_id')->unsigned();
        $table->foreign('habitacion_id')->references('id')->on('habitaciones')->onDelete('cascade');
        $table->integer('tipo_moneda_id')->unsigned();
        $table->foreign('tipo_moneda_id')->references('id')->on('tipo_moneda');
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
        Schema::drop('precios');
    }
}
