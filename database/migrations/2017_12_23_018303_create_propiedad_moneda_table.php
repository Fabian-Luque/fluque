<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropiedadMonedaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('propiedad_moneda')) {
            Schema::create('propiedad_moneda', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('propiedad_id')->unsigned();
            $table->foreign('propiedad_id')->references('id')->on('propiedades');
            $table->integer('clasificacion_moneda_id')->unsigned();
            $table->foreign('clasificacion_moneda_id')->references('id')->on('clasificacion_moneda');
            $table->integer('tipo_moneda_id')->unsigned();
            $table->foreign('tipo_moneda_id')->references('id')->on('tipo_moneda');
            $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
            Schema::drop('propiedad_moneda');
    }
}
