<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Response;
use App\Huesped;
use App\Reserva;
use App\Servicio;
use App\HuespedReserva;
use Illuminate\Support\Facades\Validator;
use App\HuespedReservaServicio;
use App\Precio;

class HuespedController extends Controller
{
    




	public function index(Request $request){


		

		if($request->has('rut')){

			$huesped = Huesped::where('rut', $request->input('rut'))->first();


			
			if(is_null($huesped)){

				$data = array(

					'msj' => "Huesped no encontrado",
					'errors' => true


				);

			return Response::json($data, 404);


			}else{

			$comentario = $huesped->calificacionPropiedades->last();

				$data = array(
					'huesped' 			   => $huesped,
					'ultimo_comentario'    => $comentario

					);

				return $data;



			}

		}


	}


	public function update(Request $request, $id){


		$rules = array(

            'nombre'                => '',
            'apellido'          	=> '',
            'rut'   				=> '',
            'email'                 => '',
            'telefono'   			=> '',
            'pais'                  => '',
            
        );

    	$validator = Validator::make($request->all(), $rules);


         if ($validator->fails()) {

            $data = [

                'errors' => true,
                'msg' => $validator->messages(),

            ];

            return Response::json($data, 400);

        } else {

            $huesped = Huesped::findOrFail($id);

            $huesped->update($request->all());
            $huesped->touch();
            
            $data = [

                'errors' => false,
                'msg' => 'Huesped actualizado satisfactoriamente',

            ];

            return Response::json($data, 201);

        }


	}


	public function ingresoHuesped(Request $request){

		if($request->has('reserva_id') && $request->has('huespedes')){
		 

		 $reserva = Reserva::where('id', $request['reserva_id'])->first();

		 
		 $huespedes = $request['huespedes'];


		   if(is_null($reserva)){

			  $retorno = array(
				'msj' 		=> "Reserva no encontrada",
				'erros'		=> true);

			  return Response::json($retorno, 404);
			  
			}else{

			foreach($huespedes as $huesped){

				$huesped = Huesped::firstOrNew($huesped);
              
                $huesped->apellido       = $huesped['apellido'];
                $huesped->rut            = $huesped['rut'];
                $huesped->telefono       = $huesped['telefono'];
                $huesped->pais           = $huesped['pais'];
                $huesped->save();
                					
                $huespedReserva = HuespedReserva::where('huesped_id', $huesped->id)->where('reserva_id', $reserva->id)->first();

                if(is_null($huespedReserva)){

                $reserva->huespedes()->attach($huesped->id);
                $reserva->update(array('estado_reserva_id' => 3));


                }else{

                $retorno = array(
				'msj' 		=> $huesped->nombre ." ". $huesped->apellido ." ya fue ingresado a la reserva",
				'erros'		=> true);

			    return Response::json($retorno, 400);


                }



			}

			$retorno = array(

				'msj' => "Huespedes ingresados correctamente",
				'erros' =>false
			);

			return Response::json($retorno, 200);

		}



		}else{

			$retorno = array(

				'msj' 	 => "La solicitud esta incompleta",
				'errors' =>	true
			);

			return Response::json($retorno, 400);


		}	


	}



	public function getHuespedes(Request $request){



			$id = $request->input('propiedad_id');

			$fecha = $request->input('fecha_a_evaluar');

			$fecha_a_evaluar = strtotime($fecha);

			$reserva_info = [];
			$huespedes_info = [];



            $huespedes = Huesped::whereHas('reservas.habitacion', function($query) use($id){

                    $query->where('propiedad_id', $id);

					})->with(['reservas' => function ($q) {

    				$q->where('estado_reserva_id', 3)->with('habitacion');}])->get();



			foreach ($huespedes as $huesped) {

				$reservas = $huesped->reservas;

					
				if(count($huesped->reservas) != 0) {

					array_push($huespedes_info, $huesped);


				}

			}

			return $huespedes_info;



	}





	public function ingresoConsumo(Request $request){


		if($request->has('consumo_servicio')){

			$consumos = $request->input('consumo_servicio');

		}

		foreach ($consumos as $consumo) {
				
		$reserva_id = $consumo['reserva_id'];
		$huesped_id = $consumo['huesped_id'];
		$servicio_id = $consumo['servicio_id'];
		$cantidad = $consumo['cantidad'];

		$reserva = Reserva::where('id', $reserva_id)->first();


		$servicio = Servicio::where('id' , $servicio_id)->first();
		$precio_servicio = Precio::where('tipo_moneda_id', $reserva->tipo_moneda_id)->where('habitacion_id', $reserva->habitacion_id)->lists('precio_habitacion')->first();

	 	$precio_total = $cantidad * $precio_servicio;


		$cantidad_disponible = $servicio->cantidad_disponible;


			if($cantidad >= 1){


				if(!is_null($servicio)){


			if($servicio->categoria_id == 2){



				 if($servicio->cantidad_disponible > 0){
				 	

				 	if($cantidad <= $servicio->cantidad_disponible){

				 	$cantidad_disponible = $cantidad_disponible - $cantidad;

				 	$servicio->update(array('cantidad_disponible' => $cantidad_disponible));

				 	if($reserva->$tipo_moneda_id == 2){
				 		return "el tipo de monedda en usd";

				 	}


					$reserva->reservasHuespedes()->attach($huesped_id, ['servicio_id' => $servicio_id, 'cantidad' => $cantidad , 'precio_total' => $precio_total]);


					$consumo =$precio_total + $reserva->monto_consumo;
					$total = $precio_total + $reserva->monto_total;
					$por_pagar =$precio_total + $reserva->monto_por_pagar;


					$reserva->update(array('monto_consumo' => $consumo, 'monto_total' => $total , 'monto_por_pagar' => $por_pagar));

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

				
			}elseif($servicio->categoria_id == 1){


					$reserva->reservasHuespedes()->attach($huesped_id, ['servicio_id' => $servicio_id, 'cantidad' => $cantidad , 'precio_total' => $precio_total]);


					$consumo =$precio_total + $reserva->monto_consumo;
					$total = $precio_total + $reserva->monto_total;
					$por_pagar =$precio_total + $reserva->monto_por_pagar;


					$reserva->update(array('monto_consumo' => $consumo, 'monto_total' => $total , 'monto_por_pagar' => $por_pagar));




			}








				}else{


					$data = array(

	                    'msj' => " No se encuentra servicio",
	                    'errors' => true


	                );

	                return Response::json($data, 404);




				}
			}else{




					$data = array(

	                    'msj' => " La cantidad ingresada no corresponde",
	                    'errors' => true


	                );

	                return Response::json($data, 400);



			}






				}

				   $data = array(

                    'msj' => "Consumo ingresado satisfactoriamente",
                    'errors' =>false
                   );

                    return Response::json($data, 201);


	}

	public function eliminarConsumo($id){


	$consumo = HuespedReservaServicio::where('id', $id)->first();

	$reserva_id = $consumo->reserva_id;

	$reserva = Reserva::where('id', $reserva_id)->first();

	$monto_consumo = $reserva->monto_consumo - $consumo->precio_total;
	$monto_total = $reserva->monto_total - $consumo->precio_total;
	$monto_por_pagar = $reserva->monto_por_pagar - $consumo->precio_total;

	$reserva->update(array('monto_consumo' => $monto_consumo, 'monto_total' => $monto_total, 'monto_por_pagar' => $monto_por_pagar));

	$consumo->delete();

	return "consumo eliminado";





	}	




}
