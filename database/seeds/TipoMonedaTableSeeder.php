<?php

use Illuminate\Database\Seeder;

class TipoMonedaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
            DB::table('tipo_moneda')->insert([
    		[
    			'nombre' => 'CLP',
                'cantidad_decimales' => 0,

    		],
    		[

    			'nombre' => 'USD',
                'cantidad_decimales' => 2,

    		]

    		]);
    }
}
