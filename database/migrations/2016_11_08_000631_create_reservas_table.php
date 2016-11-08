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
        $table->integer('precio_total');
        $table->integer('ocupacion');
        $table->date('checkin');
        $table->date('checkout');
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
