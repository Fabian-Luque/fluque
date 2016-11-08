<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetalleNochesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('detalle_noches', function(Blueprint $table){
        $table->increments('id');
        $table->integer('precio');
        $table->date('fecha');
        $table->integer('habitacion_id')->unsigned();
        $table->foreign('habitacion_id')->references('id')->on('habitaciones');
        $table->integer('reserva_id')->unsigned();
        $table->foreign('reserva_id')->references('id')->on('reservas');
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
        
        Schema::drop('detalle_noches');


    }
}
