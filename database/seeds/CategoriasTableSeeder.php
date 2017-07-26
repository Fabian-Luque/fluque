<?php

use Illuminate\Database\Seeder;

class CategoriasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       	DB::table('categorias')->insert([
    		[
    			'nombre' => 'Servicio',

    		],
    		[

    			'nombre' => 'Comida y bebestible',

    		]

    		]);
    }
}
