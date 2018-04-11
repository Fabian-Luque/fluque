<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PagoOnline extends Migration {
    public function up() {
        Schema::create(
            'pagos_online', 
            function (Blueprint $table) {
                $table->increments('id');
                $table->integer('estado');
                $table->timestamp('fecha_facturacion');
                $table->integer('pas_pago_id')->unsigned();
                $table->integer('prop_id')->unsigned();
                $table->integer('plan_id')->unsigned();

                $table->foreign('prop_id')
                    ->references('id')
                ->on('propiedades');

                $table->foreign('plan_id')
                    ->references('id')
                ->on('planes');

                $table->foreign('pas_pago_id')
                    ->references('pas_pago_id')
                ->on('pasarela_pago');
                $table->timestamps();
            }
        );
    }

    public function down() {
        Schema::drop('pagos_online');
    }
}