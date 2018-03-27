<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Pago extends Migration {
    public function up() {
        Schema::create(
            'pagos', 
            function (Blueprint $table) {
                $table->increments('id');
                $table->date('fecha_facturacion');
                $table->integer('noches');
                $table->integer('habitacion_id')->unsigned();
                $table->foreign('habitacion_id')
                    ->references('id')
                    ->on('habitaciones')
                ->onDelete('cascade');
                $table->timestamps();
            }
        );
    }

    public function down() {
    }
}
