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


            $equipamiento                    	  = new Equipamiento();


			$equipamiento ->bano              	  = $request->get('bano');
            $equipamiento ->tv                    = $request->get('tv');
            $equipamiento ->wifi                  = $request->get('wifi');
            $equipamiento ->frigobar              = $request->get('frigobar');



			$equipamiento ->habitacion_id			  = $habitacion->id; 

 			$equipamiento->save();
            

			     $data = [
                'errors' => false,
                'msg' => 'Habitacion creado satisfactoriamente',

            	];

			return Response::json($data, 201);

        }



	}  




        public function update(Request $request, $id){


        $rules = array(

            'nombre'        => '',
            'tipo'          => '',
            'precio'        => 'numeric',
            'piso'          => 'numeric',
            'bano'          => '',
            'tv'            => '',
            'wifi'          => '',
            'frigobar'      => '',
            
        );

    $validator = Validator::make($request->all(), $rules);


         if ($validator->fails()) {

            $data = [

                'errors' => true,
                'msg' => $validator->messages(),

            ];

            return Response::json($data, 400);

        } else {

            $habitacion = Habitacion::findOrFail($id);

            $habitacion->update($request->all());
            $habitacion->touch();
            


            $equipamiento = Equipamiento::findOrFail($id);

            $equipamiento->update($request->all());
            $equipamiento->touch();

            $data = [

                'errors' => false,
                'msg' => 'Habitacion actualizada satisfactoriamente',

            ];

            return Response::json($data, 201);

        }








    }





    public function destroy($id){

        $habitacion = Habitacion::findOrFail($id);
        $habitacion->delete();

        $data = [

            'errors' => false,
            'msg'    => 'Habitacion eliminada satisfactoriamente',

        ];

        return Response::json($data, 202);



    }




}
