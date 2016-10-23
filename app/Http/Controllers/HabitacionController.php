<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Http\Requests;

use Illuminate\Support\Facades\Validator;

use Response;

use App\Habitacion;

use App\Equipamiento;

use App\Propiedad;





class HabitacionController extends Controller
{
    


    public function index(Request $request){

    	  if($request->has('propiedad_id')){
            return $habitaciones = Propiedad::where('id', $request->input('propiedad_id'))->with('habitaciones.equipamiento')->get();


        }
        
    }



	public function store(Request $request){

			$rules = array(

			'nombre' 		=> 'required',
			'tipo'			=> 'required',
			'precio'		=> 'required|numeric',
			'piso'			=> 'required|numeric',
			'propiedad_id'  => 'required|numeric',
			'bano'      	=> 'required',
            'tv'        	=> 'required',
            'wifi'      	=> 'required',
            'frigobar'  	=> 'required',

			
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
          	$habitacion->piso                     = $request->get('piso'); 
          	$habitacion->propiedad_id             = $request->get('propiedad_id');
   
            $habitacion->save();


            $equipamiento                    	  	  = new Equipamiento();


            if($request->get('bano') == 'true'){
			$equipamiento ->bano              	 	  = '1';

            }else{

            if($request->get('bano') == 'false'){
            $equipamiento ->bano                      = '0';

            }
            }

            if($request->get('tv') == 'true'){

			$equipamiento ->tv              		  = '1';
            }else{

            if($request->get('tv') == 'false'){

            $equipamiento ->tv                        = '0';

            }    


            }

            if($request->get('wifi') == 'true'){


			$equipamiento ->wifi  	 			      = '1';

            }else{


            if($request->get('wifi') == 'false'){

            $equipamiento ->wifi                      = '0';

            }

            }

            if($request->get('frigobar') == 'true'){

			$equipamiento ->frigobar               	  = '1';

            }else{

            if($request->get('frigobar') == 'false'){

            $equipamiento ->frigobar                  = '0';


            }

            }

			$equipamiento ->habitacion_id			  = $habitacion->id; 

 			$equipamiento->save();
            

			     $data = [
                'errors' => false,
                'msg' => 'Habitacion creado satisfactoriamente',

            	];

			return Response::json($data, 201);

        }



	}








}
