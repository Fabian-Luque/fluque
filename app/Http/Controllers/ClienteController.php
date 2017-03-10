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



class ClienteController extends Controller
{



	public function ingresoServicio(Request $request){

		 if($request->has('venta_servicio') && $request->has('propiedad_id')){

		 	$propiedad =  Propiedad::where('id', $request->input('propiedad_id'))->first();

		 	if(!is_null($propiedad)){

		 		$servicios = $request->input('venta_servicio');

		 		foreach ($servicios as $servicio) {



		 		$nombre_consumidor = $servicio['nombre_consumidor'];
		 		$apellido_consumidor = $servicio['apellido_consumidor'];
		 		$rut_consumidor = $servicio['rut_consumidor'];
		 		$servicio_id = $servicio['servicio_id'];
                $cantidad = $servicio['cantidad'];
                $precio_total = $servicio['precio_total'];
                $cliente_id = $servicio['cliente_id'];

                $serv = Servicio::where('id', $servicio_id)->where('propiedad_id', $request->input('propiedad_id'))->first();
               	$cliente = Cliente::where('id', $cliente_id)->first();


                if(!is_null($serv)){

                $servicio_id = $serv->id;
                $servicio_nombre = $serv->nombre;
                $cantidad_disponible = $serv->cantidad_disponible;

            if($serv->categoria_id == 2){


               	if(!is_null($cliente)){


               		if($cantidad >= 1){

               			if($serv->cantidad_disponible > 0){

               				if($cantidad <= $serv->cantidad_disponible){

               				 $cantidad_disponible = $cantidad_disponible - $cantidad;

                             $serv->update(array('cantidad_disponible' => $cantidad_disponible));

			                 $propiedad->consumoClienteServicios()->attach($servicio_id, ['cliente_id' => $cliente_id,'nombre_consumidor' => $nombre_consumidor,'apellido_consumidor' => $apellido_consumidor,'rut_consumidor' => $rut_consumidor,'cantidad' => $cantidad , 'precio_total' => $precio_total]);


			            	}else{



			            	$data = array(

                            'msj' => " La cantidad ingresada es mayor al stock del producto",
                            'errors' => true


                            );

                            return Response::json($data, 400);



			            	}

           				 }else{

           			    	$data = array(

	                        'msj' => " El servicio no tiene stock",
	                        'errors' => true


	                         );

	                         return Response::json($data, 400);


	           				 }

            	}else{


	            		    $data = array(

	                        'msj' => " La cantidad ingresada no corresponde",
	                        'errors' => true


	                        );

	                        return Response::json($data, 400);



			            	}


			            	}else{


			            	$data = array(

								'msj' => "Cliente no encontrado",
								'errors' => true


							);

							return Response::json($data, 404);


			            	}



            }elseif($serv->categoria_id == 1){


            	$propiedad->consumoClienteServicios()->attach($servicio_id, ['cliente_id' => $cliente_id,'nombre_consumidor' => $nombre_consumidor,'apellido_consumidor' => $apellido_consumidor,'rut_consumidor' => $rut_consumidor,'cantidad' => $cantidad , 'precio_total' => $precio_total]);



            }



                 }else{

                $retorno = array(

                'msj'    => "El servicio no pertenece a la propiedad",
                'errors' => true
                );

                return Response::json($retorno, 400);


             }


		 		}

		 		$retorno = array(

                    'msj' => "Servicios ingresados correctamente",
                    'errors' =>false
                );

                return Response::json($retorno, 201);
		 		



		 	}else{


		 		$data = array(

                    'msj' => "Propiedad no encontrada",
                    'errors' => true


                );

            return Response::json($data, 404);



		 	}



		 }else{

		 	$retorno = array(

                'msj'    => "La solicitud esta incompleta",
                'errors' => true
            );

            return Response::json($retorno, 400);





		 }

	}


	public function getClientes(Request $request){



		if($request->has('propiedad_id') && $request->has('fecha_inicio') && $request->has('fecha_fin')){


		$propiedad_id = $request->input('propiedad_id');
		$fecha_inicio = $request->input('fecha_inicio');
		$fecha_fin = $request->input('fecha_fin');
		$rango = [$fecha_inicio, $fecha_fin];

		$propiedad = Propiedad::where('id', $propiedad_id)->first();

		if(!is_null($propiedad)){



		$clientes = Cliente::whereHas('reservas.habitacion', function($query) use($propiedad_id){

                    $query->where('propiedad_id', $propiedad_id);

                	})->whereHas('reservas', function($query) use($rango){

                    $query->whereBetween('checkin' ,$rango);

               		 })->where('tipo_cliente_id', 2)->get();
			

        		return $clientes;




		}else{


			$data = array(

                    'msj' => "Propiedad no encontrada",
                    'errors' => true
                );

            return Response::json($data, 404);


		}

			
		}else{



			$retorno = array(

	            'msj'    => "La solicitud esta incompleta",
	            'errors' => true
	        );

	        return Response::json($retorno, 400);

		}


	}



	public function buscarEmail(Request $request){


	  $email = $request->input('email');

	  $cliente = Cliente::where('email', $email)->first();

	  if(!is_null($cliente)){


			$data = array(

					'msj' 	 => "El email ya existe",
					'errors' => true

				);

			return $data;


	  }else{

	  		$data = array(

					'msj' 	 => "Email correcto",
					'errors' => false

				);

			return $data;


	  }


	}

	public function buscarRut(Request $request){


	  $rut = $request->input('rut');

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








    
	public function index(Request $request){


		if($request->has('rut')){


			$cliente_rut = $request->input('rut');

			$cliente = Cliente::where('rut', $cliente_rut)->first();

			if(is_null($cliente)){

				$data = array(

					'msj' => "Cliente no encontrado",
					'errors' => true


				);

			return Response::json($data, 404);




			}else{

			return $cliente = Cliente::where('rut', $cliente_rut)->first();



			}

		}

	}


	public function getCliente(Request $request){

		if($request->has('email')){

			$cliente_email = $request->input('email');

			$cliente = Cliente::where('email', $cliente_email)->first();

			if(is_null($cliente)){

				$data = array(

					'msj' => "Cliente no encontrado",
					'errors' => true


				);

			return Response::json($data, 404);


			}else{

				return $cliente;



			}




		}



	}



	public function calificacion(Request $request){


		$propiedad_id = $request->input('propiedad_id');
		$reserva_id = $request->input('reserva_id');
		$huespedes = $request->input('huespedes');
		$comentario_huesped = $request->input('comentario_huesped');
		$calificacion_huesped = $request->input('calificacion_huesped');

		$propiedad = Propiedad::where('id', $propiedad_id)->first();
		$reserva = Reserva::where('id', $reserva_id)->first();

		
		$reserva_checkout = strtotime($reserva->checkout);
		$fecha_hoy = strtotime('now');


		if($reserva_checkout == $fecha_hoy){

	
		$reserva->update(array('estado_reserva_id' => 4));


		foreach ($huespedes as $huesped) {

			
			$huesped_id = $huesped;

			$huesped = Huesped::where('id', $huesped_id)->first();

			$propiedad->calificacionHuespedes()->attach($huesped->id, ['comentario' => $comentario_huesped, 'calificacion' => $calificacion_huesped]);

			$numero_calificaciones = $huesped->calificacionPropiedades()->count();


			$calificacion_total = 0;
			foreach ($huesped->calificacionPropiedades as $calificacion) {
						
				$num = $calificacion->pivot->calificacion;

				$calificacion_total = $calificacion_total + $num;

				$promedio = $calificacion_total / $numero_calificaciones;

				$huesped->update(array('calificacion_promedio' => $promedio));


			}
		
		}



		}elseif($reserva_checkout < $fecha_hoy){

			$reserva->update(array('estado_reserva_id' => 4));


			foreach ($huespedes as $huesped) {

			
			$huesped_id = $huesped;

			$huesped = Huesped::where('id', $huesped_id)->first();

			$propiedad->calificacionHuespedes()->attach($huesped->id, ['comentario' => $comentario_huesped, 'calificacion' => $calificacion_huesped]);

			$numero_calificaciones = $huesped->calificacionPropiedades()->count();


			$calificacion_total = 0;
			foreach ($huesped->calificacionPropiedades as $calificacion) {
						
				$num = $calificacion->pivot->calificacion;

				$calificacion_total = $calificacion_total + $num;

				$promedio = $calificacion_total / $numero_calificaciones;

				$huesped->update(array('calificacion_promedio' => $promedio));


			}
		
		}



		}else{


			$habitacion = Habitacion::where('id', $reserva->habitacion_id)->first();

			$fecha_actual = date('Y-m-d', strtotime('now'));
			$noches = (date('j', strtotime('now')) - date("j",strtotime($reserva->checkin)));

			$precio_habitacion = $habitacion->precio_base;

			$precio_alojamiento = $noches * $precio_habitacion;

			$monto_total = $precio_alojamiento + $reserva->monto_consumo;

			$total_pagos = 0;
			foreach ($reserva->pagos as  $pago) {
				
				$total_pagos += $pago->monto_pago; 

			}

			$monto_por_pagar = $monto_total - $total_pagos;

			
			$reserva->update(array('estado_reserva_id' => 4,'monto_alojamiento' => $precio_alojamiento, 'monto_total' => $monto_total, 'monto_por_pagar' => $monto_por_pagar, 'checkout' => $fecha_actual, 'noches' => $noches));


			foreach ($huespedes as $huesped) {

			$huesped_id = $huesped;

			$huesped = Huesped::where('id', $huesped_id)->first();

			$propiedad->calificacionHuespedes()->attach($huesped->id, ['comentario' => $comentario_huesped, 'calificacion' => $calificacion_huesped]);

			$numero_calificaciones = $huesped->calificacionPropiedades()->count();


			$calificacion_total = 0;
			foreach ($huesped->calificacionPropiedades as $calificacion) {
						
				$num = $calificacion->pivot->calificacion;

				$calificacion_total = $calificacion_total + $num;

				$promedio = $calificacion_total / $numero_calificaciones;

				$huesped->update(array('calificacion_promedio' => $promedio));


			}
		
		}


		}	


		return "calificados";

	}

		public function update(Request $request, $id){


		$rules = array(

            'nombre'                => '',
            'apellido'				=> '',
            'rut'   				=> '',
            'direccion'				=> '',
            'ciudad'				=> '',
            'email'                 => '',
            'telefono'   			=> '',
            'pais'                  => '',
            'giro'					=> '',
            
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



	public function getTipoCliente(){


		$tipoCliente = TipoCliente::all();

			return $tipoCliente;



	}




}
