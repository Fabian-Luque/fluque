<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNumeroChequeToMetodoPagoPropiedadServicioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('metodo_pago_propiedad_servicio', function (Blueprint $table) {
        $table->string('numero_cheque')->after('numero_operacion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pagos', function (Blueprint $table) {
        $table->dropColumn('numero_cheque');
        });
    }
}
