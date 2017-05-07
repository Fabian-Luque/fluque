<?php

use Illuminate\Database\Seeder;

class EstadoReservaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


	    DB::table('estado_reserva')->insert([
    		[
    			'nombre' => 'Reserva sin confirmar',

    		],
    		[

    			'nombre' => 'Reserva confirmada',

    		],
    		[

    			'nombre' => 'En curso',

    		],
            [

                'nombre' => 'Finalizada',

            ],
            [

                'nombre' => 'Pendiente',

            ],
            [

                'nombre' => 'Anulada',

            ]

    		]);

    }
}
