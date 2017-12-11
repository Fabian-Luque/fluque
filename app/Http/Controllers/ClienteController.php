<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use Response;
use App\Cliente;
use App\TipoCliente;
use App\Propiedad;
use App\Huesped;
use App\Reserva;
use App\Habitacion;
use App\Servicio;
use App\PrecioServicio;
use App\Temporada;
use App\PrecioTemporada;
use App\ZonaHoraria;
use \Carbon\Carbon;



class ClienteController extends Controller
{

	public function ingresoServicio(Request $request)
	{
		if ($request->has('venta_servicio') && $request->has('propiedad_id')) {
		 	$propiedad =  Propiedad::where('id', $request->input('propiedad_id'))->first();

		 	if (!is_null($propiedad)) {
		 		$servicios = $request->input('venta_servicio');

		 		foreach ($servicios as $servicio) {
			 		$nombre_consumidor 	 = $servicio['nombre_consumidor'];
			 		$apellido_consumidor = $servicio['apellido_consumidor'];
			 		$rut_consumidor 	 = $servicio['rut_consumidor'];
			 		$servicio_id 		 = $servicio['servicio_id'];
	                $cantidad 			 = $servicio['cantidad'];
	                $cliente_id 		 = $servicio['cliente_id'];

	                $serv 				 = Servicio::where('id', $servicio_id)->where('propiedad_id', $request->input('propiedad_id'))->first();
	               	$cliente 			 = Cliente::where('id', $cliente_id)->first();

	                if (!is_null($serv)) {
		                $servicio_id 	 	 = $serv->id;
		                $servicio_nombre 	 = $serv->nombre;
		                $cantidad_disponible = $serv->cantidad_disponible;

            			if ($serv->categoria_id == 2) {
               				if (!is_null($cliente)) {
				               	$reservas 		 = Reserva::where('cliente_id', $cliente->id)->get();
				               	$reserva  		 = $reservas->last();
	                			$precio_servicio = PrecioServicio::where('tipo_moneda_id', $reserva->tipo_moneda_id)->where('servicio_id', $servicio_id)->lists('precio_servicio')->first();
	                			$precio_total    = $precio_servicio * $cantidad;

               					if ($cantidad >= 1) {
               						if ($serv->cantidad_disponible > 0) {
               							if ($cantidad <= $serv->cantidad_disponible) {
	               				 			$cantidad_disponible = $cantidad_disponible - $cantidad;
	                             			$serv->update(array('cantidad_disponible' => $cantidad_disponible));

			                 				$propiedad->consumoClienteServicios()->attach($servicio_id, ['cliente_id' => $cliente_id,'nombre_consumidor' => $nombre_consumidor,'apellido_consumidor' => $apellido_consumidor,'rut_consumidor' => $rut_consumidor,'cantidad' => $cantidad , 'precio_total' => $precio_total]);

				            			} else {
							            	$data = array(
					                            'msj' => " La cantidad ingresada es mayor al stock del producto",
					                            'errors' => true);
				                            return Response::json($data, 400);
				            			}
	           				 		} else {
			           			    	$data = array(
					                        'msj' => " El servicio no tiene stock",
					                        'errors' => true);
				                        return Response::json($data, 400);
		           					}
            					} else {
			            		    $data = array(
				                        'msj' => " La cantidad ingresada no corresponde",
				                        'errors' => true);
			                        return Response::json($data, 400);
			            		}
			            	} else {
				            	$data = array(
									'msj' => "Cliente no encontrado",
									'errors' => true);
								return Response::json($data, 404);
			            	}

            			} elseif ($serv->categoria_id == 1) {
			            	$reservas 		 =	Reserva::where('cliente_id', $cliente->id)->get();
			               	$reserva  		 = $reservas->last();
	                		$precio_servicio = PrecioServicio::where('tipo_moneda_id', $reserva->tipo_moneda_id)->where('servicio_id', $servicio_id)->lists('precio_servicio')->first();
	                		$precio_total    = $precio_servicio * $cantidad;

	            			$propiedad->consumoClienteServicios()->attach($servicio_id, ['cliente_id' => $cliente_id,'nombre_consumidor' => $nombre_consumidor,'apellido_consumidor' => $apellido_consumidor,'rut_consumidor' => $rut_consumidor,'cantidad' => $cantidad , 'precio_total' => $precio_total]);
            			}
                	} else {
		                $retorno = array(
			                'msj'    => "El servicio no pertenece a la propiedad",
			                'errors' => true);
		                return Response::json($retorno, 400);
                	}
		 		}

		 		$retorno = array(
                    'msj' => "Servicios ingresados correctamente",
                    'errors' =>false);
                return Response::json($retorno, 201);
		 	} else {
		 		$data = array(
                    'msj' => "Propiedad no encontrada",
                    'errors' => true);
            	return Response::json($data, 404);
		 	}
		} else {
		 	$retorno = array(
                'msj'    => "La solicitud esta incompleta",
                'errors' => true);
            return Response::json($retorno, 400);
		}

	}


	public function getClientes(Request $request)
	{
		if($request->has('propiedad_id') && $request->has('fecha_inicio') && $request->has('fecha_fin')){

			$propiedad_id = $request->input('propiedad_id');
			$fecha_inicio = $request->input('fecha_inicio');
			$fecha_fin 	  = $request->input('fecha_fin');
			$rango 		  = [$fecha_inicio, $fecha_fin];
			$propiedad 	  = Propiedad::where('id', $propiedad_id)->first();

			if(!is_null($propiedad)){

				$clientes = Cliente::whereHas('reservas.habitacion', function($query) use($propiedad_id){
                    $query->where('propiedad_id', $propiedad_id);
                	})->whereHas('reservas', function($query) use($rango){
                    	$query->whereBetween('checkin' ,$rango);
               		})->where('tipo_cliente_id', 2)->get();

        		return $clientes;

			} else {
				$data = array(
                    'msj' => "Propiedad no encontrada",
                    'errors' => true);
	            return Response::json($data, 404);
			}
		} else {

			$retorno = array(
	            'msj'    => "La solicitud esta incompleta",
	            'errors' => true);
	        return Response::json($retorno, 400);
		}

	}


	public function buscarRut(Request $request)
	{
		$rut 	 = $request->input('rut');
	  	$cliente = Cliente::where('rut', $rut)->first();

	  	if(!is_null($cliente)){

			$data = array(
				'msj' 	 => "El rut ya existe",
				'errors' => true
			);

			return $data;

	  	}else{

	  		$data = array(
				'msj' 	 => "Rut correcto",
				'errors' => false
			);

			return $data;
	  	}

	}


    
public function index(Request $request)
{
	if($request->has('rut')){

		$cliente_rut = $request->input('rut');
		$cliente 	 = Cliente::where('rut', $cliente_rut)->first();

		if(is_null($cliente)){

			$data = array(
				'msj' => "Cliente no encontrado",
				'errors' => true);
			return Response::json($data, 404);

		}else{

		return $cliente = Cliente::where('rut', $cliente_rut)->with('pais', 'region')->first();

		}
	}


}


public function calificacion(Request $request)
{
    if ($request->has('propiedad_id')) {
        $propiedad_id = $request->input('propiedad_id');
        $propiedad    = Propiedad::where('id', $propiedad_id)->first();
        if (is_null($propiedad)) {
            $retorno = array(
                'msj'    => "Propiedad no encontrada",
                'errors' => true);
            return Response::json($retorno, 404);
        }
    } else {
        $retorno = array(
            'msj'    => "No se envia propiedad_id",
            'errors' => true);
        return Response::json($retorno, 400);
    }
   	if ($request->has('reserva_id')) {
   		$reserva_id = $request->input('reserva_id');
   		$reserva    = Reserva::where('id', $reserva_id)->with('pagos')->first();
   		if (is_null($reserva)) {
   			$retorno = array(
                'msj'    => "Propiedad no encontrada",
                'errors' => true);
            return Response::json($retorno, 404);
   		}
   	} else {
   		$retorno = array(
            'msj'    => "No se envia reserva_id",
            'errors' => true);
        return Response::json($retorno, 400);
   	}
   	
   	if ($request->has('huespedes') && $request->has('calificacion_huesped')) {
		$huespedes 			  = $request->input('huespedes');
		$comentario_huesped	  = $request->input('comentario_huesped');
		$calificacion_huesped = $request->input('calificacion_huesped');
   	} else {
   		$retorno = array(
            'msj'    => "La solicitud esta incompleta",
            'errors' => true);
        return Response::json($retorno, 400);       		
   	}


   	$zona_horaria   = ZonaHoraria::where('id', $propiedad->zona_horaria_id)->first();
    $pais           = $zona_horaria->nombre;
	$actual    		= Carbon::now();
	$hoy       		= $actual->tz($pais)->startOfDay();
	$fecha_hoy 		= $hoy->format('Y-m-d');
	$checkout 		= new Carbon($reserva->checkout);
	$checkin  		= new Carbon($reserva->checkin);

	$fecha_checkout = $checkout->format('Y-m-d');
	$fecha_checkin  = $checkin->format('Y-m-d');
   	
	if($fecha_checkout == $fecha_hoy || $fecha_checkout < $fecha_hoy){
   		$pago = $reserva->pagos->where('metodo_pago_id', 2)->first();

   		if (!is_null($pago)) {
			$reserva->update(array('estado_reserva_id' => 5));
   		} else {
   			$reserva->update(array('estado_reserva_id' => 4));
   		}
		
		foreach ($huespedes as $huesped) {
			$huesped_id = $huesped;
			$huesped 	= Huesped::where('id', $huesped_id)->first();
			$propiedad->calificacionHuespedes()->attach($huesped->id, ['comentario' => $comentario_huesped, 'calificacion' => $calificacion_huesped]);
			$numero_calificaciones = $huesped->calificacionPropiedades()->count();

			$calificacion_total = 0;
			foreach ($huesped->calificacionPropiedades as $calificacion) {
				$num 				= $calificacion->pivot->calificacion;
				$calificacion_total = $calificacion_total + $num;
				$promedio 			= $calificacion_total / $numero_calificaciones;
				$huesped->update(array('calificacion_promedio' => $promedio));
			}
		}

	} elseif ($fecha_checkin < $fecha_hoy && $fecha_checkout > $fecha_hoy) {
       	$auxFecha = new Carbon($fecha_checkin);
       	$auxFin   = new Carbon($fecha_hoy);
       	$noches   = $auxFin->diffInDays($auxFecha);
       	$checkout = $auxFin->format('Y-m-d');
   		$pago     = $reserva->pagos->where('metodo_pago_id', 2)->first();

        if (!is_null($pago)) {
			$reserva->update(array('estado_reserva_id'=> 5, 'checkout' => $checkout, 'noches' => $noches));
   		} else {
   			$reserva->update(array('estado_reserva_id'=> 4, 'checkout' => $checkout, 'noches' => $noches));
   		}

		foreach ($huespedes as $huesped) {
			$huesped_id = $huesped;
			$huesped 	= Huesped::where('id', $huesped_id)->first();
			$propiedad->calificacionHuespedes()->attach($huesped->id, ['comentario' => $comentario_huesped, 'calificacion' => $calificacion_huesped]);
			$numero_calificaciones = $huesped->calificacionPropiedades()->count();

			$calificacion_total = 0;
			foreach ($huesped->calificacionPropiedades as $calificacion) {
				$num 				= $calificacion->pivot->calificacion;
				$calificacion_total = $calificacion_total + $num;
				$promedio 			= $calificacion_total / $numero_calificaciones;
				$huesped->update(array('calificacion_promedio' => $promedio));
			}
		}

	} else {
		$retorno = array(
            'msj'    => "La reserva aún no se ha cursado",
            'errors' => true);
        return Response::json($retorno, 400);
	}

	return "calificados";
}


public function update(Request $request, $id)
{
	$rules = array(

        'nombre'                => '',
        'apellido'				=> '',
        'rut'   				=> '',
        'direccion'				=> '',
        'ciudad'				=> '',
        'email'                 => '',
        'telefono'   			=> '',
        'giro'					=> '',
        'pais_id'               => '',
        'region_id'             => '',
        
    );

	$validator = Validator::make($request->all(), $rules);

     if ($validator->fails()) {

        $data = [
            'errors' => true,
            'msg' => $validator->messages(),
        ];

        return Response::json($data, 400);

    } else {

        $cliente = Cliente::findOrFail($id);
        $cliente->update($request->all());
        $cliente->touch();
        
        $data = [
            'errors' => false,
            'msg' => 'Cliente actualizado satisfactoriamente',
        ];
        return Response::json($data, 201);

    }

}


public function getTipoCliente()
{

	$tipoCliente = TipoCliente::all();
	return $tipoCliente;

}



}
