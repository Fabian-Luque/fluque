<?php

use Illuminate\Database\Seeder;

class TipoComprobanteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
    		    DB::table('tipo_comprobante')->insert([
    		[
    			'nombre' => 'Factura nacional',

    		],
    		[

    			'nombre' => 'Factura exportación',

    		],
            [

                'nombre' => 'Boleta',

            ]

    		]);


    }
}
