<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePagoFacil extends Migration {
    public function up() { 
        if (!Schema::hasTable('pago_facil')) {
            Schema::create(
                'pago_facil', 
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('order_id');
                    $table->double('monto');
                    $table->string('email');
                    $table->string('status');
                    $table->integer('pago_id')->unsigned();
                    $table->foreign('pago_id')
                        ->references('id')
                    ->on('pagos_online');
                    $table->timestamps();
                }
            );
        }
    }

    public function down() {
    }
}