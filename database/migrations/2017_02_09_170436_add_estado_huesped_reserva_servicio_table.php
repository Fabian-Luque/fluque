<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEstadoHuespedReservaServicioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('huesped_reserva_servicio', function (Blueprint $table) {
        $table->enum('estado', ['Por pagar','Pagado'])->after('precio_total');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('huesped_reserva_servicio', function (Blueprint $table) {
        $table->dropColumn('estado');
        });
    }
}
