<?php

use Illuminate\Database\Seeder;

class EstadoCajaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('estado_caja')->insert([
    		[
    			'nombre' => 'Abierta',

    		],
    		[

    			'nombre' => 'Cerrada',

    		]

    	]);
    }
}
