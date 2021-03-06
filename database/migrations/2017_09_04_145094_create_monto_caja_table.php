<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMontoCajaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('montos_caja')) {
        Schema::create('montos_caja', function (Blueprint $table) {
            $table->increments('id');
            $table->float('monto',10,2);
            $table->integer('caja_id')->unsigned();
            $table->foreign('caja_id')->references('id')->on('cajas')->onDelete('cascade');
            $table->integer('tipo_moneda_id')->unsigned();
            $table->foreign('tipo_moneda_id')->references('id')->on('tipo_moneda');
            $table->integer('tipo_monto_id')->unsigned();
            $table->foreign('tipo_monto_id')->references('id')->on('tipo_monto');
            $table->timestamps();
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
