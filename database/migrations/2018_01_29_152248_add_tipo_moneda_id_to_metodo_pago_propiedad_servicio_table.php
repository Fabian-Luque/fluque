<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTipoMonedaIdToMetodoPagoPropiedadServicioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('metodo_pago_propiedad_servicio', function (Blueprint $table) {
            $table->integer('tipo_moneda_id')->after('tipo_comprobante_id')->nullable()->unsigned();
            $table->foreign('tipo_moneda_id')->references('id')->on('tipo_moneda');
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
