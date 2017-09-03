<?php

use Illuminate\Database\Seeder;

class EstadosdeCuenta extends Seeder {
    public function run() {
		DB::table('estado_cuenta')->insert(
            array(
                'nombre' => 'prueba'
            ),
            array(
                'nombre' => 'activa'
            ),
            array(
                'nombre' => 'inactiva'
            )
        );
    }
}