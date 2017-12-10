<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCajaIdToPagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('pagos')) {
        Schema::table('pagos', function (Blueprint $table) {
            $table->integer('caja_id')->after('tipo_comprobante_id')->nullable()->unsigned();
            $table->foreign('caja_id')->references('id')->on('cajas');
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
