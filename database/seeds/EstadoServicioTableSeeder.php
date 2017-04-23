<?php

use Illuminate\Database\Seeder;

class EstadoServicioTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        	DB::table('estado_servicio')->insert([
    		[
    			'nombre' => 'Disponible',

    		],
    		[

    			'nombre' => 'Sin configurar',

    		]

    		]);
    }
}
