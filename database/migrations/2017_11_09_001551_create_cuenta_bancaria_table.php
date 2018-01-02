<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCuentaBancariaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('cuenta_bancaria')) {
        Schema::create('cuenta_bancaria', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre_banco');
            $table->string('numero_cuenta');
            $table->string('titular');
            $table->string('rut');
            $table->string('email');
            $table->integer('tipo_cuenta_id')->unsigned();
            $table->foreign('tipo_cuenta_id')->references('id')->on('tipo_cuenta')->onDelete('cascade');
            $table->integer('propiedad_id')->unsigned();
            $table->foreign('propiedad_id')->references('id')->on('propiedades')->onDelete('cascade');
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
