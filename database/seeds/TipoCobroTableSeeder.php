<?php

use Illuminate\Database\Seeder;

class TipoCobroTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_cobro')->insert([
    		[
    			'nombre' => 'Por habitación',

    		],
    		[

    			'nombre' => 'Por persona',

    		],
    		[

    			'nombre' => 'Por ocupación',

    		]

    		]);
    }
}
