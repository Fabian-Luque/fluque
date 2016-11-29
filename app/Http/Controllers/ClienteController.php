<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Response;
use App\Cliente;
use App\TipoCliente;

class ClienteController extends Controller
{
    
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


	public function getTipoCliente(){


		$tipoCliente = TipoCliente::all();

			return $tipoCliente;



	}




}
