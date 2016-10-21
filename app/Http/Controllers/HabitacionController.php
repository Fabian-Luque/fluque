<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Http\Requests;

use Illuminate\Support\Facades\Validator;

use Response;

use App\Habitacion;

use App\Equipamiento;





class HabitacionController extends Controller
{
    

	public function store(Request $request){

			$rules = array(

			'nombre' 		=> 'required',
			'tipo'			=> 'required',
			'precio'		=> 'required|numeric',	
			'propiedad_id'  => 'required|numeric',
			'bano'      	=> 'numeric',
            'tv'        	=> 'numeric',
            'wifi'      	=> 'numeric',
            'frigobar'  	=> 'numeric',

			
		);

			$validator = Validator::make($request->all(), $rules);


 	     if ($validator->fails()) {

            $data = [

                'errors' => true,
                'msg' => $validator->messages(),

            ];

            return Response::json($data, 400);

        } else {


            $habitacion                           = new Habitacion();
            $habitacion->nombre          	      = $request->get('nombre');
           	$habitacion->tipo           	      = $request->get('tipo');
          	$habitacion->precio                   = $request->get('precio');
          	$habitacion->propiedad_id             = $request->get('propiedad_id');
   
            $habitacion->save();


            $equipamiento                    	  	  = new Equipamiento();
			$equipamiento ->bano              	 	  = $request->get('bano');
			$equipamiento ->tv              		  = $request->get('tv');
			$equipamiento ->wifi  	 			      = $request->get('wifi');
			$equipamiento ->frigobar               	  = $request->get('frigobar');

			$equipamiento ->habitacion_id			  = $habitacion->id; 

 			$equipamiento->save();
            

			     $data = [
                'errors' => false,
                'msg' => 'usuario creado satisfactoriamente',

            	];

			return Response::json($data, 201);

        }



	}








}
