<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Http\Requests;

use Illuminate\Support\Facades\Validator;

use Response;

use App\Servicio;

use App\Propiedad;

class ServicioController extends Controller
{


    ///////////////prueba////////////////////////

    
		public function index(Request $request){

    	  if($request->has('propiedad_id')){
            return $servicios = Propiedad::where('id', $request->input('propiedad_id'))->with('servicios')->get();


        }
        
    }



		public function store(Request $request){

			$rules = array(

			'nombre' 		=> 'required',
			'categoria'		=> 'required',
			'precio'		=> 'required|numeric',
			'propiedad_id'  => 'required|numeric',


			
		);

			$validator = Validator::make($request->all(), $rules);


 	     if ($validator->fails()) {

            $data = [

                'errors' => true,
                'msg' => $validator->messages(),

            ];

            return Response::json($data, 400);

        } else {


            $servicio                             = new Servicio();
            $servicio->nombre          	          = $request->get('nombre');
           	$servicio->categoria           	      = $request->get('categoria');
          	$servicio->precio                     = $request->get('precio');
          	$servicio->propiedad_id               = $request->get('propiedad_id');
   
            $servicio->save();

            

			     $data = [
                'errors' => false,
                'msg' => 'Servicio creado satisfactoriamente',

            	];

			return Response::json($data, 201);

        }



	}  


	    public function update(Request $request, $id){

			$rules = array(

			'nombre' 		=> '',
			'categoria'		=> '',
			'precio'		=> 'numeric',


			
		);

			$validator = Validator::make($request->all(), $rules);


 	     if ($validator->fails()) {

            $data = [

                'errors' => true,
                'msg' => $validator->messages(),

            ];

            return Response::json($data, 400);

        } else {


            $servicio = Servicio::findOrFail($id);

            $servicio->update($request->all());
            $servicio->touch();

            $data = [

                'errors' => false,
                'msg' => 'Servicio actualizado satisfactoriamente',

            ];

            return Response::json($data, 201);

        }



	}  


	    public function destroy($id){

        $servicio = Servicio::findOrFail($id);
        $servicio->delete();

        $data = [

            'errors' => false,
            'msg'    => 'Servicio eliminado satisfactoriamente',

        ];

        return Response::json($data, 202);



    }







}
