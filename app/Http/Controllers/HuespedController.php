<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Huesped;

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


}
