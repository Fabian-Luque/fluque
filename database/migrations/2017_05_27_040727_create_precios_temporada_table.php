<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePreciosTemporadaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('precios_temporada', function (Blueprint $table) {
            $table->increments('id');
            $table->float('precio',10,2);
            $table->integer('tipo_habitacion_id')->unsigned();
            $table->foreign('tipo_habitacion_id')->references('id')->on('tipo_habitacion')->onDelete('cascade');
            $table->integer('tipo_moneda_id')->nullable()->unsigned();
            $table->foreign('tipo_moneda_id')->references('id')->on('tipo_moneda')->onDelete('set null');
            $table->integer('temporada_id')->unsigned();
            $table->foreign('temporada_id')->references('id')->on('temporadas')->onDelete('cascade');
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
        Schema::drop('precios_temporada');
    }
}
