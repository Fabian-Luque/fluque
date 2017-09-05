<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEstadoCuentaIdToPropiedadesTabla extends Migration {
    public function up() {
        Schema::table(
            'propiedades', 
            function (Blueprint $table) {
                $table->integer('estado_cuenta_id')
                    ->after('pais_id')
                    ->nullable()
                ->unsigned();
                $table->foreign('estado_cuenta_id')
                    ->references('id')
                ->on('estado_cuenta');
            }
        );  
    }

    public function down() {
        Schema::table(
            'propiedades', 
            function (Blueprint $table) {
                $table->dropColumn(
                    'estado_cuenta_id'
                );
            }
        );
    }
}