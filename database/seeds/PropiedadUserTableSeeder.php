<?php

use Illuminate\Database\Seeder;

class PropiedadUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       DB::table('propiedad_user')->insert([
     		[
     			'user_id' 		=> 1,
    			'propiedad_id'  => 1,
     		],
     		[
     			'user_id' 		=> 2,
    			'propiedad_id'  => 2,
     		],
     		[
     			'user_id' 		=> 3,
    			'propiedad_id'  => 3,
     		],
     		[
     			'user_id' 		=> 4,
    			'propiedad_id'  => 4,
     		],
     		[
     			'user_id' 		=> 5,
    			'propiedad_id'  => 5,
     		],
     		[
     			'user_id' 		=> 6,
    			'propiedad_id'  => 6,
     		],
     		[
     			'user_id' 		=> 7,
    			'propiedad_id'  => 7,
     		],
     		[
     			'user_id' 		=> 8,
    			'propiedad_id'  => 8,
     		],
     		[
     			'user_id' 		=> 9,
    			'propiedad_id'  => 9,
     		],
     		[
     			'user_id' 		=> 10,
    			'propiedad_id'  => 10,
     		],
     		[
     			'user_id' 		=> 11,
    			'propiedad_id'  => 11,
     		],
     		[
     			'user_id' 		=> 12,
    			'propiedad_id'  => 12,
     		],
     		[
     			'user_id' 		=> 13,
    			'propiedad_id'  => 13,
     		],
     		[
     			'user_id' 		=> 14,
    			'propiedad_id'  => 14,
     		],
     		[
     			'user_id' 		=> 15,
    			'propiedad_id'  => 15,
     		],
     		[
     			'user_id' 		=> 16,
    			'propiedad_id'  => 16,
     		],
     		[
     			'user_id' 		=> 17,
    			'propiedad_id'  => 17,
     		],
     		[
     			'user_id' 		=> 18,
    			'propiedad_id'  => 18,
     		],
     		[
     			'user_id' 		=> 19,
    			'propiedad_id'  => 19,
     		],
     		[
     			'user_id' 		=> 20,
    			'propiedad_id'  => 20,
     		]
        ]);
    }
}
