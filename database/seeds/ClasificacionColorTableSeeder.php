<?php

use Illuminate\Database\Seeder;

class ClasificacionColorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('clasificacion_color')->insert([
    		[
    			'nombre' => 'Primario',

    		],
    		[

    			'nombre' => 'Acentuado',

    		]

    		]);
    }
}
