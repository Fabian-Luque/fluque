<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMontoContratoPropiedades extends Migration {

    public function up() {
        Schema::table(
            'propiedades', 
            function (Blueprint $table) {
                $table->integer('monto_contrato')
                    ->after('id')
                ->default(0);
            }
        );
    }

    public function down() {
    }
}