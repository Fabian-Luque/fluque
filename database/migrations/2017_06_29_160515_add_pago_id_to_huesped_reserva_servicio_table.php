<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPagoIdToHuespedReservaServicioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('huesped_reserva_servicio', function (Blueprint $table) {
        $table->integer('pago_id')->after('servicio_id')->nullable()->unsigned();
        $table->foreign('pago_id')->references('id')->on('pagos')->onDelete('set null');
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
