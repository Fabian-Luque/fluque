<?php

use Illuminate\Database\Seeder;

class SeccionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('secciones')->insert([
    		[
    			'id'     => 1,
    			'nombre' => 'Configuración',

    		],
    		[
    			'id'     => 2,
    			'nombre' => 'Reservas',

    		],
    		[
    			'id'     => 3,
    			'nombre' => 'Editar',

    		],
    		[
    			'id'     => 4,
    			'nombre' => 'Reportes',

    		],
    		[
    			'id'     => 5,
    			'nombre' => 'Habitaciones',

    		],
    		[
    			'id'     => 6,
    			'nombre' => 'Productos y servicios',

    		],
    		[
    			'id'     => 7,
    			'nombre' => 'Consumos',

    		]

    		]);
    }
}
