<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTipoHabitacionToReservasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('reservas')) {
        Schema::table('reservas', function (Blueprint $table) {
            $table->integer('tipo_habitacion_id')->after('habitacion_id')->unsigned()->nullable();
            $table->foreign('tipo_habitacion_id')->references('id')->on('tipo_habitacion');
        });

        Schema::table('reservas', function(Blueprint $table){
            $table->integer('numero_reserva')->nullable()->change();
        });

        Schema::table('reservas', function(Blueprint $table){
            $table->dropForeign('reservas_habitacion_id_foreign');
            $table->foreign('habitacion_id')->references('id')->on('habitaciones')->nullable();
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
        //
    }
}
