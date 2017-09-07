<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHuespedReservaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('huesped_reserva', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('huesped_id')->unsigned()->index();
            $table->foreign('huesped_id')->references('id')->on('huespedes');
            $table->integer('reserva_id')->unsigned()->index();
            $table->foreign('reserva_id')->references('id')->on('reservas');
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
        

        Schema::drop('huesped_reserva');


    }
}
