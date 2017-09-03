<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEstadoCuenta extends Migration {
    public function up() {
        Schema::create(
            'estado_cuenta', 
            function(Blueprint $table) {
                $table->increments('id');
                $table->string('nombre');
                $table->timestamps();
            }
        );
    }
    public function down() {
        Schema::drop('estado_cuenta');
    }
}
