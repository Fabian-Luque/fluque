<?php

use Illuminate\Database\Seeder;

class MetodoPagoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
    	DB::table('metodo_pago')->insert([
    		[
    			'nombre' => 'Efectivo',

    		],
    		[

    			'nombre' => 'Credito',

    		],
    		[

    			'nombre' => 'Cheque',

    		]

    		]);





    }
}
