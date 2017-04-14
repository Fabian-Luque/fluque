<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePreciosServicioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('precios_servicio', function(Blueprint $table){
        $table->increments('id');
        $table->float('precio_servicio',10,2)->nullable();
        $table->integer('servicio_id')->unsigned();
        $table->foreign('servicio_id')->references('id')->on('servicios')->onDelete('cascade');
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
        Schema::drop('precios_servicio');
    }
}
