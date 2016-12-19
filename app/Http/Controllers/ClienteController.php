<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Response;
use App\Cliente;
use App\TipoCliente;
use App\Propiedad;

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


	public function calificacion(Request $request){


		$propiedad_id = $request->input('propiedad_id');
		$huespedes = $request->input('huespedes');
		$cliente_id = $request->input('cliente_id');
		$calificacion_cliente = $request->input('calificacion_cliente');
		$comentario_cliente = $request->input('comentario_cliente');




		$propiedad = Propiedad::where('id', $propiedad_id)->first();

		$cliente = Cliente::where('id', $cliente_id)->first();


		$propiedad->calificacionClientes()->attach($cliente_id,['comentario' => $comentario_cliente, 'calificacion' => $calificacion_cliente]);


		$n_calificaciones = $cliente->calificacionPropiedades()->count();

		$suma_calificacion = 0;

		foreach($cliente->calificacionPropiedades as $calificacion){

			$numero = $calificacion->pivot->calificacion;
			$suma_calificacion = $suma_calificacion + $numero;


		}

		$calificacion_promedio = $suma_calificacion / $n_calificaciones;
		$cliente->update(array('calificacion_promedio' => $calificacion_promedio));


		return "calificados";




	}







	public function getTipoCliente(){


		$tipoCliente = TipoCliente::all();

			return $tipoCliente;



	}




}
