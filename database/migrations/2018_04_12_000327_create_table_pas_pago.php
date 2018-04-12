<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePasPago extends Migration {
    public function up() {
        Schema::create(
            'pasarela_pago', 
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('nombre');
                $table->integer('pas_pago_id');
            }
        );

        DB::table('pasarela_pago')->insert([
            [
                'nombre' => 'pagofacil',
                'pas_pago_id' => 1
            ]
        ]);
    }

    public function down() {
        Schema::drop('pasarela_pago');
    }
}