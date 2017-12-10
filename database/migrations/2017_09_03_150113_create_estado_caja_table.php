<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEstadoCajaTable extends Migration {

    public function up()
    {
        if (!Schema::hasTable('egreso_caja')) {
        Schema::create('estado_caja', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre');
            $table->timestamps();
        });
    }
    }


    public function down()
    {
        //
    }
}
