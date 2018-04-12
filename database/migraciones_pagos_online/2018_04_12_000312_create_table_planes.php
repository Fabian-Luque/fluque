<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePlanes extends Migration {
    public function up() {
        if (!Schema::hasTable('planes')) {
            Schema::create(
                'planes', 
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('facturacion');
                    $table->double('precio_x_habitacion');
                }
            );

            DB::table('planes')->insert([
                [
                    'facturacion' => 'mensual',
                    'precio_x_habitacion' => 1800
                ],
                [
                    'facturacion' => 'semestral',
                    'precio_x_habitacion' => 1800
                ],
                [
                    'facturacion' => 'anual',
                    'precio_x_habitacion' => 1800
                ]
            ]);
        }
    }

    public function down() {
        if (Schema::hasTable('planes')) {
            Schema::drop('pago_facil');
            Schema::drop('pagos_online');
            Schema::drop('pasarela_pago');
            Schema::drop('planes');
        }
    }
}