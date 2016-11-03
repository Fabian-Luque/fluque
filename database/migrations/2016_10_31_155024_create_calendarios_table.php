<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalendariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
            Schema::create('calendarios', function(Blueprint $table){
            $table->increments('id');
            $table->integer('disponibilidad');
            $table->integer('reservas');
            $table->integer('precio');
            $table->date('fecha');
            $table->integer('habitacion_id')->unsigned();
            $table->foreign('habitacion_id')->references('id')->on('habitaciones')->onDelete('cascade');
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
        Schema::drop('calendarios');
    }
}
