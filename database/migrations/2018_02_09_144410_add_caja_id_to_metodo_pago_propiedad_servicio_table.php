<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCajaIdToMetodoPagoPropiedadServicioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('metodo_pago_propiedad_servicio', function (Blueprint $table) {
            $table->integer('caja_id')->after('tipo_moneda_id')->nullable()->unsigned();
            $table->foreign('caja_id')->references('id')->on('cajas')->onDelete('cascade');
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
