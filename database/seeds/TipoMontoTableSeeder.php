<?php

use Illuminate\Database\Seeder;

class TipoMontoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_monto')->insert([
    		[
    			'nombre' => 'Apertura',

    		],
    		[

    			'nombre' => 'Cierre',

    		]

    	]);
    }
}
