<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTipoMonedaIdToPagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('pagos')) {
        Schema::table('pagos', function (Blueprint $table) {
        $table->float('monto_equivalente',10,2)->after('monto_pago');
        $table->integer('tipo_moneda_id')->after('numero_operacion')->nullable()->unsigned();
        $table->foreign('tipo_moneda_id')->references('id')->on('tipo_moneda');
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
