<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Response;
use App\Huesped;
use App\Reserva;

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

				return $huesped;



			}

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
                					
                $reserva->huespedes()->attach($huesped->id);



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
		$precio_total = $consumo['precio_total'];

		$reserva = Reserva::where('id', $reserva_id)->first();
		$reserva->reservasHuespedes()->attach($huesped_id, ['servicio_id' => $servicio_id, 'cantidad' => $cantidad , 'precio_total' => $precio_total]);


		$consumo =$precio_total + $reserva->monto_consumo;
		$total = $precio_total + $reserva->monto_total;
		$por_pagar =$precio_total + $reserva->monto_por_pagar;


		$reserva->update(array('monto_consumo' => $consumo, 'monto_total' => $total , 'monto_por_pagar' => $por_pagar));


/*		$suma_servicios = 0;
		foreach ($reserva->servicios as $servicio) {
			
			$precio = $servicio->pivot->precio_total;

			
			$suma_servicios = $suma_servicios + $precio;



		}*/

		}

		return "consumo agregado";


	}




}
