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

			'nombre' 						=> '',
			'tipo'	 						=> '',
			'numero_habitaciones'			=> 'numeric',
			'pais' 							=> '',
			'ciudad'					    => '', 
			'estado'					    => '', 
			'direccion'					    => '',
			'telefono'					    => '',
			'email'					        => '',
			'nombre_responsable'	        => '',
			'descripcion'					=> '',
			'moneda'					    => '',
			
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
