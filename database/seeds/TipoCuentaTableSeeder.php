<?php

use Illuminate\Database\Seeder;

class TipoCuentaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_cuenta')->insert([
    		[
    			'nombre' => 'Cuenta corriente',

    		],
    		[

    			'nombre' => 'Cuenta vista',

    		],
            [

                'nombre' => 'Cuenta de ahorro',

            ]

    		]);
    }
}
