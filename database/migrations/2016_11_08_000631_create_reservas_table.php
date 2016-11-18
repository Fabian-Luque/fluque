<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('reservas', function(Blueprint $table){
        $table->increments('id');
        $table->integer('monto_total');
        $table->integer('monto_sugerido');
        $table->string('metodo_pago');
        $table->integer('ocupacion');
        $table->string('fuente');
        $table->date('checkin');
        $table->date('checkout');
        $table->string('estado');
        $table->integer('habitacion_id')->unsigned();
        $table->foreign('habitacion_id')->references('id')->on('habitaciones');
        $table->integer('cliente_id')->unsigned();
        $table->foreign('cliente_id')->references('id')->on('clientes');
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
        
        Schema::drop('reservas');

    }
}
