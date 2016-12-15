<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use App\Propiedad;
use Response;
use App\TipoPropiedad;







class PropiedadController extends Controller
{


    public function index(Request $request){

        if($request->has('id')){
            $propiedad = Propiedad::where('id', $request->input('id'))->with('tipoPropiedad')->get();
            return $propiedad;

        }




    }


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
			'tipo_propiedad_id'	 			=> 'required|numeric',
			'numero_habitaciones'			=> 'required|numeric',
			'pais' 							=> 'required',
			'ciudad'					    => 'required', 
			'region'					    => 'required',
			'direccion'					    => 'required',
			'telefono'					    => 'required',
			'email'					        => 'required',
			'nombre_responsable'	        => 'required',
			'descripcion'					=> 'required',
            'iva'                           => 'required|numeric',
            'porcentaje_deposito'           => 'required|numeric',
            'pago'                          => 'numeric',
			
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


    public function getTipoPropiedad(){

    $TipoPropiedad = TipoPropiedad::all();
        return $TipoPropiedad; 
    }


}
