<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use App\Propiedad;
use Response;







class PropiedadController extends Controller
{


		public function show($id){

		  try {
            return Propiedad::where('id', $id)->get();
        } catch (ModelNotFoundException $e) {
            $data = [
                'errors' => true,
                'msg'    => $e->getMessage(),
            ];
            return Response::json($data, 404);
        }


	}




    

	public function update(Request $request, $id){


		$rules = array(

			'nombre' 						=> 'required',
			'tipo'	 						=> 'required|numeric',
			'numero_habitaciones'			=> 'required|numeric',
			'pais' 							=> 'required',
			'ciudad'					    => 'required', 
			'region'					    => 'required',
			'direccion'					    => 'required',
			'telefono'					    => 'required',
			'email'					        => 'required',
			'nombre_responsable'	        => 'required',
			'descripcion'					=> 'required',
			
		);

 	$validator = Validator::make($request->all(), $rules);


 	     if ($validator->fails()) {

            $data = [

                'errors' => true,
                'msg' => $validator->messages(),

            ];

            return Response::json($data, 400);

        } else {

            $propiedad = Propiedad::findOrFail($id);
            $propiedad->update($request->all());
            $propiedad->touch();

            $data = [

                'errors' => false,
                'msg' => 'Propiedad actualizada satisfactoriamente',

            ];

            return Response::json($data, 201);

        }








	}



}
