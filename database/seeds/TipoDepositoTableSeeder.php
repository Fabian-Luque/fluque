<?php

use Illuminate\Database\Seeder;

class TipoDepositoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_deposito')->insert([
    		[
    			'nombre' => 'Porcentaje',

    		],
    		[

    			'nombre' => 'Cantidad de noches',

    		],
            [

                'nombre' => 'Ninguno',

            ]

    		]);
    }
}
