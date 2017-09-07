<?php

use Illuminate\Database\Seeder;

class TipoClienteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
          DB::table('tipo_cliente')->insert([
    		[
    			'nombre' => 'particular',

    		],
    		[

    			'nombre' => 'empresa',

    		]

    		]);
    }
}
