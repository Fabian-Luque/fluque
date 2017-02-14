<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAtributosMetodoPagoPropiedadServicioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('metodo_pago_propiedad_servicio', function (Blueprint $table) {
        $table->string('numero_operacion')->after('precio_total')->nullable();
        $table->integer('tipo_comprobante_id')->after('metodo_pago_id')->nullable()->unsigned();
        $table->foreign('tipo_comprobante_id')->references('id')->on('tipo_comprobante');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('metodo_pago_propiedad_servicio', function (Blueprint $table) {
        $table->dropColumn('numero_operacion');
        $table->dropColumn('tipo_comprobante_id');
        });
    }
}
