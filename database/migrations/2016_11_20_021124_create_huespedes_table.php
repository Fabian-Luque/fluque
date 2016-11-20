<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHuespedesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        

        Schema::create('huespedes', function(Blueprint $table){
        $table->increments('id');
        $table->string('nombre');
        $table->string('apellido');
        $table->string('rut')->unique();
        $table->string('email');
        $table->integer('telefono');
        $table->integer('reserva_id')->unsigned();
        $table->foreign('reserva_id')->references('id')->on('reservas')->onDelete('cascade');
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
        Schema::drop('huespedes');
    }
}
