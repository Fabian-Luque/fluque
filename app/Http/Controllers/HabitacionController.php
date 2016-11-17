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
use App\Calendario;
use Carbon\Carbon;





class HabitacionController extends Controller
{
    /**
     * se obtiene las habitaciones disponibles en un rango de fechas
     *
     * @author ALLEN
     *
     * @param  Request          $request (propiedad_id, fecha_inicio, fecha_fin)
     * @return Response::json
     */

    public function Disponibilidad(Request $request){

        $fecha_inicio = $request->input('fecha_inicio');
        $fecha_fin    = $request->input('fecha_fin');
            




        $rango = [$fecha_inicio, $fecha_fin];

        $dias = ((strtotime($fecha_fin)-strtotime($fecha_inicio))/86400)+1;



        if($request->has('propiedad_id')){

         $habitaciones = Habitacion::where('propiedad_id', $request->input('propiedad_id'))->whereHas('calendarios', function($query) use($rango) {
           $query->whereBetween('fecha',  $rango)->where('reservas', 0);}, '=', $dias)->get();

        return $habitaciones;

    }


    }



    public function index(Request $request){

    	  if($request->has('propiedad_id')){
            return $habitaciones = Propiedad::where('id', $request->input('propiedad_id'))->with('habitaciones.equipamiento')->get();


        }
        
    }



	public function store(Request $request){

			$rules = array(

			'nombre' 		       => 'required',
			'tipo'			       => 'required',
			'precio_base'	       => 'required|numeric',
            'disponibilidad_base'  => 'required|numeric',
			'piso'			       => 'required|numeric',
			'propiedad_id'         => 'required|numeric',
			'bano'      	       => 'required',
            'tv'        	       => 'required',
            'wifi'      	       => 'required',
            'frigobar'  	       => 'required',

			
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
          	$habitacion->precio_base              = $request->get('precio_base');
            $habitacion->disponibilidad_base      = $request->get('disponibilidad_base');
          	$habitacion->piso                     = $request->get('piso'); 
          	$habitacion->propiedad_id             = $request->get('propiedad_id');
            $habitacion->save();

            $equipamiento                    	  = new Equipamiento();
			$equipamiento ->bano              	  = $request->get('bano');
            $equipamiento ->tv                    = $request->get('tv');
            $equipamiento ->wifi                  = $request->get('wifi');
            $equipamiento ->frigobar              = $request->get('frigobar');
			$equipamiento ->habitacion_id		  = $habitacion->id; 
 			$equipamiento->save();


            $habitacion_tipo    = $habitacion->id;
            $habitacion_precio  = $habitacion->precio_base;
            $fecha_inicio       = '1 January, 2016';
            $fecha_fin          = '31 December, 2016';
            $fecha              = date ("Y-m-d",strtotime($fecha_inicio));

            $habitacion_base = Habitacion::find($habitacion_tipo);


            while (strtotime($fecha) <= strtotime($fecha_fin)) {
                
                

           $habitacion_dia =  Calendario::firstOrNew(array('fecha'=>$fecha, 'disponibilidad' => $habitacion_base->disponibilidad_base, 'precio' => $habitacion_base->precio_base,'habitacion_id' => $habitacion_tipo));

           $habitacion_dia->save();

           $fecha = date ("Y-m-d", strtotime("+1 day", strtotime($fecha)));



            }


			     $data = [
                'errors' => false,
                'msg' => 'Habitacion creado satisfactoriamente',

            	];

			return Response::json($data, 201);

        }



	}  




        public function update(Request $request, $id){


        $rules = array(

            'nombre'                => '',
            'tipo'                  => '',
            'precio_base'           => 'numeric',
            'disponibilidad_base'   => 'numeric',
            'piso'                  => 'numeric',
            'bano'                  => '',
            'tv'                    => '',
            'wifi'                  => '',
            'frigobar'              => '',
            
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





/*        public function destroy($id){

        $habitacion = Habitacion::findOrFail($id);
        $habitacion->delete();

        $data = [

            'errors' => false,
            'msg'    => 'Habitacion eliminada satisfactoriamente',

        ];

        return Response::json($data, 202);



    }*/


    public function destroy($id)
    {
        try {
             $habitacion = Habitacion::findOrFail($id);
      	     $habitacion->delete();
            $data = [
                'errors' => false,
                'msg'    => 'Habitacion eliminada satisfactoriamente',
            ];
            return Response::json($data, 202);
        } catch (QueryException $e) {
            $data = [
                'errors' => true,
                'msg'    => 'An error ocurred',
            ];
            return Response::json($data, 500);
        } catch (ModelNotFoundException $e) {
            $data = [
                'errors' => true,
                'msg'    => $e->getMessage(),
            ];
            return Response::json($data, 404);
        }
    }




}





/*

    public function index()
    {
        try {
            $response = ApiUser::all();
            if(!$response) {
                return (new Response([
                    'error' => [
                        'message' => 'No users were found.',
                        'code' => '20'
                    ]
                ], 404));
            }
            else {
                return (new Response([
                    'data' => $response
                ],200));
            }
        } catch (\Exception $e) {
            return (new Response([
                'error' => 'Users could not be loaded. Method failed.',
                'code' => 50
            ],500));
        }
    }


*/



