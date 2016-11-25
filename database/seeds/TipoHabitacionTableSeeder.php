<?php

use Illuminate\Database\Seeder;

class TipoHabitacionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {	
        

    	DB::table('tipo_habitacion')->insert([
    		[
    			'nombre' => 'Individual',

    		],
    		[

    			'nombre' => 'Doble',


    		],

    		[

    			'nombre' => 'Tiple',


    		],
    		[

    			'nombre' => 'Cuadruple',


    		],

			[

				'nombre' => 'Quintuple',


			],

			[

				'nombre' => 'Matrimonial',

			],

			[


				'nombre' => 'Suite',

			],

			[

				'nombre' => 'presidencial',
			]

    		]);


    }	
}
