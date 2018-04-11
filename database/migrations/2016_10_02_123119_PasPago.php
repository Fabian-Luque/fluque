<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PasPago extends Migration {
    public function up() {/*
        Schema::create(
            'pasarela_pago', 
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('nombre');
            }
        );

        DB::table('pasarela_pago')->insert([
            [
                'nombre' => 'pagofacil'
            ]
        ]);
        */
    }

    public function down() {
        Schema::drop('pasarela_pago');
    }
}