<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropForeignkeyPropiedadesEstadoCuentaId extends Migration {
    public function up() {
        Schema::table(
            'propiedades', 
            function(Blueprint $table) {
                $table->dropForeign(
                    'propiedades_estado_cuenta_id_foreign'
                );
            }
        );
    }

    public function down() {
    }
}