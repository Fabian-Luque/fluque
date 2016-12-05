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
        $table->integer('monto_por_pagar');
        $table->integer('ocupacion');
        $table->date('checkin');
        $table->date('checkout');
        $table->integer('noches');
        $table->integer('tipo_fuente_id')->unsigned();
        $table->foreign('tipo_fuente_id')->references('id')->on('tipo_fuente');
        $table->integer('metodo_pago_id')->unsigned();
        $table->foreign('metodo_pago_id')->references('id')->on('metodo_pago');
        $table->integer('estado_reserva_id')->unsigned();
        $table->foreign('estado_reserva_id')->references('id')->on('estado_reserva');
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
