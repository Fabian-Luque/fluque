<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPorcentajeDescPlanes extends Migration {
    public function up() {
        if (!Schema::hasColumn('planes', 'porcentaje_desc')) {
            Schema::table(
                'planes', 
                function (Blueprint $table) {
                    $table->double('porcentaje_desc');
                }
            );

            DB::table('planes')->insert([
                [
                    'facturacion' => 'gratis',
                    'precio_x_habitacion' => 0,
                    'porcentaje_desc' => 0
                ]
            ]);

            DB::table('planes')
                ->where('facturacion', 'mensual')
            ->update(['porcentaje_desc' => 0]);

            DB::table('planes')
                ->where('facturacion', 'semestral')
            ->update(['porcentaje_desc' => 3]);

            DB::table('planes')
                ->where('facturacion', 'anual')
            ->update(['porcentaje_desc' => 10]);
        }
    }

    public function down() {
    }
}
