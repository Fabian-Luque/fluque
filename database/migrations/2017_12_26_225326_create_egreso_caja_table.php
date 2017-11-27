<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEgresoCajaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('egreso_caja', function (Blueprint $table) {
            $table->increments('id');
            $table->float('monto',10,2);
            $table->string('descripcion')->nullable();
            $table->integer('egreso_id')->unsigned();
            $table->foreign('egreso_id')->references('id')->on('egresos');
            $table->integer('caja_id')->unsigned();
            $table->foreign('caja_id')->references('id')->on('cajas')->onDelete('cascade');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('tipo_moneda_id')->unsigned();
            $table->foreign('tipo_moneda_id')->references('id')->on('tipo_moneda');
            $table->timestamps();
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
