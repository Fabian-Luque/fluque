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



}
