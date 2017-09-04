<?php

use Illuminate\Database\Seeder;

class EstadosdeCuenta extends Seeder {
    public function run() {
		DB::table('estado_cuenta')->insert(
            array(
                'id' => 1,
                'nombre' => 'prueba'
            )
        );
        DB::table('estado_cuenta')->insert(
            array(
                'id' => 2,
                'nombre' => 'activa'
            )
        );
        DB::table('estado_cuenta')->insert(
            array(
                'id' => 3,
                'nombre' => 'inactiva'
            )
        );
    }
}