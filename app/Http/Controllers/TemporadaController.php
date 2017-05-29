<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use Response;
use App\Temporada;
use App\Propiedad;
use App\Calendario;

class TemporadaController extends Controller
{

    public function index(Request $request)
    {

        if ($request->has('propiedad_id')) {
            
            $propiedad = Propiedad::where('id', $request->input('propiedad_id'))->first();
            if (!is_null($propiedad)) {
                
                $temporadas = Temporada::where('propiedad_id', $request->input('propiedad_id'))->get();
                return $temporadas;

            }else{

                return "no se encuentra propiedad";

            }




        }else{

            return "no se envia propiedad_id";



        }


    }

    public function store(Request $request)
    {

    	$rules = array(

			'nombre' 		       => 'required',
            'color'                => 'required',
			'propiedad_id'         => 'required|numeric',

		);

    	$validator = Validator::make($request->all(), $rules);



 	     if ($validator->fails()) {

            $data = [

                'errors' => true,
                'msg' => $validator->messages(),

            ];

            return Response::json($data, 400);

        }else{

        	$temporada = Temporada::create($request->all());

    		$data = [
                'errors' => false,
                'msg'    => 'Temporada creada satisfactoriamente',
            ];
            return Response::json($data, 201);


        }

    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),
            [
                'nombre'   	   => '',
                'color'        => '',
            ]
        );

        if ($validator->fails()) {
            $data = [
                'errors' => true,
                'msg'    => $validator->messages(),
            ];
            return Response::json($data, 400);
        } else {
            try {
                $temporada = Temporada::findOrFail($id);
                $temporada->update($request->all());
                $temporada->touch();
            } catch (QueryException $e) {
                $data = [
                    'errors' => true,
                    'msg'    => $e->message(),
                ];
                return Response::json($data, 400);
            } catch (ModelNotFoundException $e) {
                $data = [
                    'errors' => true,
                    'msg'    => $e->getMessage(),
                ];
                return Response::json($data, 404);
            }
            $data = [
                'errors' => false,
                'msg'    => 'Temporada actualizada satisfactoriamente',
            ];
            return Response::json($data, 201);
        }
    }



        public function calendario(Request $request){

        if($request->has('fechas') && $request->has('propiedad_id')){

           $propiedad_id = $request->get('propiedad_id');
           $propiedad = Propiedad::where('id', $request->get('propiedad_id'))->first();

           if(!is_null($propiedad)){

                foreach ($request['fechas'] as $fecha) {

                   $fecha_calendario = Calendario::whereHas('temporada', function($query) use($propiedad_id){

                    $query->where('propiedad_id', $propiedad_id);

                    })->where('fecha', $fecha['fecha'])->first();

                   if (is_null($fecha_calendario)) {

                    $calendario                    = new calendario();
                    $calendario->fecha             = $fecha['fecha'];
                    $calendario->temporada_id      = $fecha['temporada_id'];
                    $calendario->save();
                       
                   }else{

                    $fecha_calendario->update(array('temporada_id' => $fecha['temporada_id']));

                   }



                }

                return "Guardado";


           }else{

            return "propiedad no existe";


           }


        }else{

            return "solicitud incompleta";


        }

    }


/*    public function calendario(Request $request){

        if($request->has('fechas') && $request->has('propiedad_id')){

           $propiedad = Propiedad::where('id', $request->get('propiedad_id'))->first();

           if(!is_null($propiedad)){

                foreach ($request['fechas'] as $fecha) {
                    $calendario                    = new calendario();
                    $calendario->fecha             = $fecha['fecha'];
                    $calendario->temporada_id      = $fecha['temporada_id'];
                    $calendario->save();

                }

                return "Guardado";


           }else{

            return "propiedad no existe";


           }


        }else{

            return "solicitud incompleta";


        }

    }*/










}
