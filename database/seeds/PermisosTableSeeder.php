<?php

use Illuminate\Database\Seeder;

class PermisosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permisos')->insert([
    		[
    			'nombre' 		=> 'Administración',
    			'seccion_id'	=>  1,

    		],
    		[
    			'nombre' 		=> 'Propiedad',
    			'seccion_id'	=>  1,

    		],
    		[
    			'nombre' 		=> 'Comercial',
    			'seccion_id'	=>  1,

    		],
    		[
    			'nombre' 		=> 'Temporada',
    			'seccion_id'	=>  1,

    		],
    		[
    			'nombre' 		=> 'Tipo habitación',
    			'seccion_id'	=>  1,

    		],
    		[
    			'nombre' 		=> 'Precios',
    			'seccion_id'	=>  1,

    		],
    		[
    			'nombre' 		=> 'Roles',
    			'seccion_id'	=>  1,

    		],
    		[
    			'nombre' 		=> 'Usuarios',
    			'seccion_id'	=>  1,

    		],
    		[
    			'nombre' 		=> 'Crear reserva',
    			'seccion_id'	=>  2,

    		],
    		[
    			'nombre' 		=> 'Realizar checkin',
    			'seccion_id'	=>  2,

    		],
    		[
    			'nombre' 		=> 'Realizar checkout',
    			'seccion_id'	=>  2,

    		],
    		[
    			'nombre' 		=> 'Agregar pago o abono',
    			'seccion_id'	=>  2,

    		],
    		[
    			'nombre' 		=> 'Cambiar a no show',
    			'seccion_id'	=>  2,

    		],
    		[
    			'nombre' 		=> 'Anular reserva',
    			'seccion_id'	=>  2,

    		],
    		[
    			'nombre' 		=> 'Reserva',
    			'seccion_id'	=>  3,

    		],
    		[
    			'nombre' 		=> 'Pagos',
    			'seccion_id'	=> 3,

    		],
    		[
    			'nombre' 		=> 'Montos',
    			'seccion_id'	=> 3,

    		],
    		[
    			'nombre' 		=> 'Consumos',
    			'seccion_id'	=> 3,

    		],
    		[
    			'nombre' 		=> 'Cliente',
    			'seccion_id'	=> 3,

    		],
    		[
    			'nombre' 		=> 'Huésped',
    			'seccion_id'	=> 3,

    		],
    		[
    			'nombre' 		=> 'General',
    			'seccion_id'	=> 4,

    		],
    		[
    			'nombre' 		=> 'Pagos',
    			'seccion_id'	=> 4,

    		],
    		[
    			'nombre' 		=> 'Financiero',
    			'seccion_id'	=> 4,

    		],
    		[
    			'nombre' 		=> 'Crear habitación',
    			'seccion_id'	=> 5,

    		],
    		[
    			'nombre' 		=> 'Editar Habitación',
    			'seccion_id'	=> 5,

    		],
    		[
    			'nombre' 		=> 'Crear productos y servicios',
    			'seccion_id'	=> 6,

    		],
    		[
    			'nombre' 		=> 'Editar productos y servicios',
    			'seccion_id'	=> 6,

    		],
    		[
    			'nombre' 		=> 'Asignar consumos',
    			'seccion_id'	=> 7,

    		]
    		
    		]);
    }
}
