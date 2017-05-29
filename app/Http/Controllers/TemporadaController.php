<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use Response;
use Carbon\Carbon;
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

    public function getCalendario(Request $request)
    {
        
        /*set_time_limit(1000000);*/

        /*$now = Carbon::now();*/

        $fechaInicio = '2017-05-01';
        $fechaFin = '2017-06-02';

        /*$comienzo = $now->startOfMonth(); */       //primer dia del mes
       /* $termino = $comienzo->addYears(1);*/         //suma un año a fecha comienzo

/*        $comienzo_mes = $comienzo->format('Y-m-d');

        $termino_mes = $termino->format('Y-m-d');*/

        $propiedad_id = $request->get('propiedad_id');

/*        $calendario = Calendario::whereHas('temporada', function($query) use($propiedad_id){

            $query->where('propiedad_id', $propiedad_id);

        })->where('fecha', '>=' , $fechaInicio)->where('fecha', '<' , $fechaFin)->get();*/

        $auxTemporada = 0;
        $periodos = [];
        $dias = [];
        $auxFecha = new Carbon($fechaInicio);   
        while ($auxFecha <= $fechaFin ) {

            $fecha = Calendario::whereHas('temporada', function($query) use($propiedad_id){

                $query->where('propiedad_id', $propiedad_id);

            })->where('fecha', $auxFecha)->with('temporada')->first();

            if (!is_null($fecha)) {

                if ($auxTemporada == $fecha->temporada_id) {


                    $day = ["fecha" => $fecha->fecha];
                    array_push($dias, $day);



                }else{

                    if ($auxTemporada == 0) {
                        
                    $day = ["fecha" => $fecha->fecha];
                    array_push($dias, $day);

                    }else{

                    if (count($dias) != 0) {
                   
                         $color_temporada = Temporada::where('id', $auxTemporada)->first();

                         $periodo = ['temporada_id' => $auxTemporada, 'color' => $color_temporada->color, 'dias' => $dias];

                         array_push($periodos, $periodo);
                         
                         $dias = [];

                         $day = ["fecha" => $fecha->fecha];
                         array_push($dias, $day);

                        }else{

                         $day = ["fecha" => $fecha->fecha];
                         array_push($dias, $day);



                        }
                    }

                        
                        $auxTemporada = $fecha->temporada_id;
                }

            }else{

                if ($auxTemporada != 0) {

                    if (count($dias) != 0 ) {

                    $color_temporada = Temporada::where('id', $auxTemporada)->first();

                    $periodo = ['temporada_id' => $auxTemporada, 'color' => $color_temporada->color, 'dias' => $dias];
                    array_push($periodos, $periodo);


                    $dias = [];
                        
                    }

                    
                }
            }


        $auxFecha->addDay();

        }// fin while


        return $periodos;




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
