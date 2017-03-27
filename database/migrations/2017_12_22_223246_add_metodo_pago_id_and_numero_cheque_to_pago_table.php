<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMetodoPagoIdAndNumeroChequeToPagoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pagos', function (Blueprint $table) {
        $table->integer('metodo_pago_id')->after('numero_operacion')->nullable()->unsigned();
        $table->foreign('metodo_pago_id')->references('id')->on('metodo_pago');
        $table->string('numero_cheque')->after('tipo')->nullable();
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
