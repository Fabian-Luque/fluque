<?php

use Illuminate\Database\Seeder;

class ColorMotorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('colores_motor')->insert([
    		[
    			'nombre' => 'red',
    			'color' => '#ffffff',
    			'background_color' => '#F44336',

    		],
    		[
    			'nombre' => 'pink',
    			'color' => '#ffffff',
    			'background_color' => '#E91E63',

    		],
    		[
    			'nombre' =>  'purple',
    			'color' =>  '#ffffff',
    			'background_color' =>  '#9C27B0',

    		],
    		[
    			'nombre' =>  'deep-purple',
    			'color' =>  '#ffffff',
    			'background_color' =>  '#673AB7',

    		],
    		[
    			'nombre' =>  'indigo',
    			'color' =>  '#ffffff',
    			'background_color' =>  '#3F51B5',

    		],
    		[
    			'nombre' =>  'blue',
    			'color' =>  '#ffffff',
    			'background_color' =>  '#2196F3',

    		],
    		[
    			'nombre' =>  'light-blue',
    			'color' =>  '#ffffff',
    			'background_color' =>  '#03A9F4',

    		],
    		[
    			'nombre' => 'cyan',
    			'color' =>  '#ffffff',
    			'background_color' =>  '#00BCD4',

    		],
    		[
    			'nombre' =>  'teal',
    			'color' =>  '#ffffff',
    			'background_color' =>  '#009688',

    		],
    		[
    			'nombre' =>  'green',
    			'color' =>  '#ffffff',
    			'background_color' =>  '#4CAF50',

    		],
    		[
    			'nombre' =>  'light-green',
    			'color' =>  '#212121',
    			'background_color' =>  '#8BC34A',

    		],
    		[
    			'nombre' =>  'lime',
    			'color' =>  '#212121',
    			'background_color' =>  '#CDDC39',

    		],
    		[
    			'nombre' =>  'yellow',
    			'color' =>  '#212121',
    			'background_color' =>  '#FFEB3B',

    		],
    		[
    			'nombre' =>  'amber',
    			'color' =>  '#212121',
    			'background_color' =>  '#FFC107',

    		],
    		[
    			'nombre' =>  'orange',
    			'color' =>  '#212121',
    			'background_color' =>  '#FF9800',

    		],
    		[
    			'nombre' => 'deep-orange',
    			'color' =>  '#ffffff',
    			'background_color' =>  '#FF5722',

    		],
    		[
    			'nombre' =>  'brown',
    			'color' =>  '#ffffff',
    			'background_color' =>  '#795548',

    		],
    		[
    			'nombre' =>  'grey',
    			'color' =>  '#ffffff',
    			'background_color' =>  '#9E9E9E',

    		],
    		[
    			'nombre' =>  'blue-grey',
    			'color' =>  '#ffffff',
    			'background_color' =>  '#607D8B',

    		]
    		
    		]);
    }
}
