<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEstadoTableMensajeria extends Migration {

    public function up() {
        Schema::table(
            'mensajeria', 
            function (Blueprint $table) {
                $table->boolean('estado')
                    ->unsigned()
                    ->nullable()
                    ->after('mensaje')
                ->default(0);
            }
        );
    }

    public function down() {
    }
}