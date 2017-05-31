<?php

namespace App\Http\Controllers;

use App\Calendario;
use App\Propiedad;
use App\Temporada;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Response;
use App\TipoHabitacion;
use App\TipoMoneda;

class TemporadaController extends Controller
{

    public function index(Request $request)
    {

        if ($request->has('propiedad_id')) {

            $propiedad = Propiedad::where('id', $request->input('propiedad_id'))->first();
            if (!is_null($propiedad)) {

                $temporadas = Temporada::where('propiedad_id', $request->input('propiedad_id'))->get();
                return $temporadas;

            } else {

                return "no se encuentra propiedad";

            }

        } else {

            return "no se envia propiedad_id";

        }

    }

    public function store(Request $request)
    {

        $rules = array(

            'nombre'       => 'required',
            'color'        => 'required',
            'propiedad_id' => 'required|numeric',

        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            $data = [

                'errors' => true,
                'msg'    => $validator->messages(),

            ];

            return Response::json($data, 400);

        } else {

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
                'nombre' => '',
                'color'  => '',
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

    public function calendario(Request $request)
    {

        if ($request->has('fechas') && $request->has('propiedad_id')) {

            $propiedad_id = $request->get('propiedad_id');
            $propiedad    = Propiedad::where('id', $request->get('propiedad_id'))->first();

            if (!is_null($propiedad)) {

                foreach ($request['fechas'] as $fecha) {

                    $fecha_calendario = Calendario::whereHas('temporada', function ($query) use ($propiedad_id) {

                        $query->where('propiedad_id', $propiedad_id);

                    })->where('fecha', $fecha['fecha'])->first();

                    if (is_null($fecha_calendario)) {

                        $calendario               = new calendario();
                        $calendario->fecha        = $fecha['fecha'];
                        $calendario->temporada_id = $fecha['temporada_id'];
                        $calendario->save();

                    } else {

                        $fecha_calendario->update(array('temporada_id' => $fecha['temporada_id']));

                    }

                }

                return "Guardado";

            } else {

                return "propiedad no existe";

            }

        } else {

            return "solicitud incompleta";

        }

    }

    public function getCalendario(Request $request)
    {

        if ($request->has('propiedad_id')) {

            $propiedad = Propiedad::where('id', $request->input('propiedad_id'))->first();

            if (!is_null($propiedad)) {

                $propiedad_id  = $request->get('propiedad_id');
                $now           = Carbon::now();

                $comienzo      = $now->startOfMonth(); //primer dia del mes
                $fecha_inicio  = $comienzo->format('Y-m-d');

                $termino       = $comienzo->addYears(1); //suma un año a fecha comienzo
                $fecha_termino = $termino->format('Y-m-d');

                $auxTemporada  = 0;
                $periodos      = [];
                $dias          = [];
                $auxInicio     = new Carbon($fecha_inicio);
                $auxFin        = new Carbon($fecha_termino);

                while ($auxInicio <= $auxFin) {

                    $fecha = Calendario::whereHas('temporada', function ($query) use ($propiedad_id) {

                        $query->where('propiedad_id', $propiedad_id);

                    })->where('fecha', $auxInicio)->with('temporada')->first();

                    if (!is_null($fecha)) {

                        if ($auxTemporada == $fecha->temporada_id) {

                            $day = ["fecha" => $fecha->fecha];
                            array_push($dias, $day);

                        } else {

                            if ($auxTemporada == 0) {

                                $day = ["fecha" => $fecha->fecha];
                                array_push($dias, $day);

                            } else {

                                if (count($dias) != 0) {

                                    $color_temporada = Temporada::where('id', $auxTemporada)->first();

                                    $periodo = ['temporada_id' => $auxTemporada, 'color' => $color_temporada->color, 'dias' => $dias];

                                    array_push($periodos, $periodo);

                                    $dias = [];

                                    $day = ["fecha" => $fecha->fecha];
                                    array_push($dias, $day);

                                } else {

                                    $day = ["fecha" => $fecha->fecha];
                                    array_push($dias, $day);

                                }
                            }

                            $auxTemporada = $fecha->temporada_id;
                        }

                    } else {

                        if ($auxTemporada != 0) {

                            if (count($dias) != 0) {

                                $color_temporada = Temporada::where('id', $auxTemporada)->first();

                                $periodo = ['temporada_id' => $auxTemporada, 'color' => $color_temporada->color, 'dias' => $dias];
                                array_push($periodos, $periodo);

                                $dias = [];

                            }

                        }
                    }

                    $auxInicio->addDay();

                } // fin while

                return $periodos;
            } else {

                return "no se encuentra propiedad";

            }

        } else {

            return "no se envia propiedad_id";

        }

    }

    public function eliminarCalendario(Request $request)
    {

        if ($request->has('fechas') && $request->has('propiedad_id')) {

            $propiedad_id = $request->get('propiedad_id');
            $propiedad    = Propiedad::where('id', $request->get('propiedad_id'))->first();

            if (!is_null($propiedad)) {

                foreach ($request['fechas'] as $fecha) {

                    $fecha_calendario = Calendario::whereHas('temporada', function ($query) use ($propiedad_id) {

                        $query->where('propiedad_id', $propiedad_id);

                    })->where('fecha', $fecha['fecha'])->first();

                    if (!is_null($fecha_calendario)) {

                        $fecha_calendario->delete();

                    } else {

                        return "La fecha " . $fecha['fecha'] . " no existe";

                    }

                }

                return "Eliminado";

            } else {

                return "propiedad no existe";

            }

        } else {

            return "solicitud incompleta";

        }

    }

    public function getPreciosTemporadas(Request $request)
    {

            /*$propiedad_id = $request->input('propiedad_id');*/

            $temporada_id = $request->input('temporada_id');



            $tipos_habitacion = TipoHabitacion::all();


            foreach ($tipos_habitacion as $tipo) {

                $tipo_habitacion_id = $tipo->id;

                $tipo_moneda = TipoMoneda::whereHas('preciosTemporada', function($query) use($temporada_id, $tipo_habitacion_id){
                    
                    $query->where('temporada_id', $temporada_id)->where('tipo_habitacion_id', $tipo_habitacion_id);})->with(['preciosTemporada' => function ($q) use($temporada_id) {

                $q->where('temporada_id', $temporada_id);}])->get();

                  

                $tipo->tipos_moneda = $tipo_moneda;
              
                    /*return $tipo;*/
                
            }

            return $tipos_habitacion;
           


    }


    public function destroy($id){

        $temporada = Temporada::findOrFail($id);
        $temporada->delete();

        $data = [

            'errors' => false,
            'msg'    => 'Temporada eliminada satisfactoriamente',

        ];

        return Response::json($data, 202);



    }






}
