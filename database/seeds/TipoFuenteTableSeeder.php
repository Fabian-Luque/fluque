<?php

use Illuminate\Database\Seeder;

class TipoFuenteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_fuente')->insert([
    		[
    			'nombre' => 'Pagina web',

    		],
    		[

    			'nombre' => 'Caminando',

    		],
    		[

    			'nombre' => 'Telefono',

    		],
    		[

    			'nombre' => 'Email',

    		],
    		[

    			'nombre' => 'Redes sociales',

    		]

    		]);
    }
}
