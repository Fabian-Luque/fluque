<?php

use Illuminate\Database\Seeder;

class ClasificacionMonedaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
           	DB::table('clasificacion_moneda')->insert([
    		[
    			'nombre' => 'Nacional',

    		],
    		[

    			'nombre' => 'Internacional',

    		]

    		]);
    }
}
