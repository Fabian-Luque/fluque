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
        DB::table('estado')->insert([
    		[
    			'nombre' => 'Abierto',

    		],
    		[

    			'nombre' => 'Cerrado',

    		]

    	]);
    }
}
