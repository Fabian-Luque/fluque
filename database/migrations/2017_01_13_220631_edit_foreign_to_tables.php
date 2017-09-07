<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditForeignToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('habitaciones', function(Blueprint $table){
        $table->dropForeign('habitaciones_propiedad_id_foreign');
        $table->foreign('propiedad_id')->references('id')->on('propiedades')->onDelete('cascade');
        });

        Schema::table('servicios', function(Blueprint $table){
        $table->dropForeign('servicios_propiedad_id_foreign');
        $table->foreign('propiedad_id')->references('id')->on('propiedades')->onDelete('cascade');
        });

        Schema::table('huesped_reserva', function(Blueprint $table){
        $table->dropForeign('huesped_reserva_reserva_id_foreign');
        $table->foreign('reserva_id')->references('id')->on('reservas')->onDelete('cascade');
        });

        Schema::table('pagos', function(Blueprint $table){
        $table->dropForeign('pagos_reserva_id_foreign');
        $table->foreign('reserva_id')->references('id')->on('reservas')->onDelete('cascade');
        });
    
        Schema::table('huesped_reserva_servicio', function(Blueprint $table){
        $table->dropForeign('huesped_reserva_servicio_reserva_id_foreign');
        $table->foreign('reserva_id')->references('id')->on('reservas')->onDelete('cascade');
        });





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
