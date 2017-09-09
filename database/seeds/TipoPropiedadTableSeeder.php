<?php

use Illuminate\Database\Seeder;

class TipoPropiedadTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
    	    	DB::table('tipo_propiedad')->insert([
    		[
    			'nombre' => 'Hotel',

    		],
    		[

    			'nombre' => 'Hostal',

    		]

    		]);



    }
}
