<?php

use Illuminate\Database\Seeder;

class EstadoHabitacionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
            DB::table('estado_habitacion')->insert([
    		[
    			'nombre' => 'Disponible',

    		],
    		[

    			'nombre' => 'Sin configurar',

    		]

    		]);
    }
}
