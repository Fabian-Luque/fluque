<?php

namespace App\Http\Controllers;

use App\Calendario;
use App\Propiedad;
use App\Temporada;
use App\TipoHabitacion;
use App\TipoMoneda;
use App\PrecioTemporada;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Response;

class TemporadaController extends Controller
{

    public function index(Request $request)
    {
        if ($request->has('propiedad_id')) {
            $propiedad_id   = $request->input('propiedad_id');
            $propiedad      = Propiedad::where('id', $propiedad_id)->first();
            if (is_null($propiedad)) {
                $retorno  = array(
                    'msj'    => "Propiedad no encontrada",
                    'errors' => true);
                return Response::json($retorno, 404);
            }
        } else {
            $retorno = array(
                'msj'    => "No se envia propiedad_id",
                'errors' => true);
            return Response::json($retorno, 400);
        }

        $temporadas = Temporada::where('propiedad_id', $propiedad_id)->get();
        return $temporadas;
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

            $temporada        = Temporada::create($request->all());
            $propiedad        = Propiedad::where('id', $request->input('propiedad_id'))->first();
            $tipos_habitacion = $propiedad->tiposHabitacion;
            $moneda_propiedad = $propiedad->tipoMonedas;

            if (count($tipos_habitacion) != 0) {
                if ($propiedad->tipo_cobro_id != 3) {
                    foreach ($tipos_habitacion as $tipo) {
                        foreach ($moneda_propiedad as $moneda) {
                            $precio_temporada                     = new PrecioTemporada();
                            $precio_temporada->cantidad_huespedes = 1;
                            $precio_temporada->precio             = 0;
                            $precio_temporada->tipo_habitacion_id = $tipo->id;
                            $precio_temporada->tipo_moneda_id     = $moneda->id;
                            $precio_temporada->temporada_id       = $temporada->id;
                            $precio_temporada->save();
                        }
                    }
                    
                }else{

                    foreach ($tipos_habitacion as $tipo) {
                        $capacidad = $tipo->capacidad;
                        foreach ($moneda_propiedad as $moneda) {
                            for ($i=1; $i <= $capacidad  ; $i++) {

                                $precio_temporada                     = new PrecioTemporada();
                                $precio_temporada->cantidad_huespedes = $i;
                                $precio_temporada->precio             = 0;
                                $precio_temporada->tipo_habitacion_id = $tipo->id;
                                $precio_temporada->tipo_moneda_id     = $moneda->id;
                                $precio_temporada->temporada_id       = $temporada->id;
                                $precio_temporada->save();   
                            }
                        }
                    }
                }
            }

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

                $data = [
                'errors' => false,
                'msg'    => 'Guardado',
            ];
            return Response::json($data, 201);

            } else {


                $data = [
                    'errors' => true,
                    'msg'    => 'No se encuentra propiedad',

                ];

                return Response::json($data, 404);

            }

        } else {


            $data = [
                    'errors' => true,
                    'msg'    => 'Solicitud incompleta',

                ];

                return Response::json($data, 400);

        }

    }

    public function obtenerCalendario(Request $request)
    {
        if ($request->has('propiedad_id')) {
            $propiedad_id = $request->input('propiedad_id');
            $propiedad    = Propiedad::where('id', $propiedad_id)->with('tipoMonedas')->first();
            if (is_null($propiedad)) {
                $retorno = array(
                    'msj'    => "Propiedad no encontrada",
                    'errors' => true);
                return Response::json($retorno, 404);
            }
        } else {
            $retorno = array(
                'msj'    => "No se envia propiedad_id",
                'errors' => true);
            return Response::json($retorno, 400);
        }

        if ($request->has('inicio') && $request->has('fin')) {
            $inicio = $request->inicio;
            $fin    = $request->fin;

        } else {
            $retorno = array(
                'errors' => true,
                'msg'    => 'Solicitud incompleta',);
            return Response::json($data, 400);
        }

        $temporadas = Temporada::where('propiedad_id', $request->propiedad_id)
        ->with(['calendarios' => function ($q) use($inicio, $fin){
            $q
              ->where('fecha', '>=' ,$inicio)
              ->where('fecha', '<=' , $fin);
        }])
        ->get();

        return $temporadas;

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

                $data = [

                'errors' => false,
                'msg' => 'Eliminado',

                ];

                return Response::json($data, 202);


            } else {

                $data = [
                    'errors' => true,
                    'msg'    => 'No se encuentra propiedad',

                ];

                return Response::json($data, 404);

            }

        } else {

            $data = [
                    'errors' => true,
                    'msg'    => 'Solicitud incompleta',

                ];

                return Response::json($data, 400);

        }

    }

    public function getPreciosTemporadas(Request $request)
    {
        if ($request->has('temporada_id')) {
            $temporada_id = $request->input('temporada_id');
            $temporada    = Temporada::where('id', $temporada_id)->first();
            if (is_null($temporada)) {
                $retorno  = array(
                    'msj'    => "Temporada no encontrada",
                    'errors' => true);
                return Response::json($retorno, 404);
            }
        } else {
            $retorno = array(
                'msj'    => "No se envia temporada_id",
                'errors' => true);
            return Response::json($retorno, 400);
        }

        $propiedad        = Propiedad::where('id', $temporada->propiedad_id)->first();
        $moneda_propiedad = $propiedad->tipoMonedas;
        $tipos_habitacion = TipoHabitacion::where('propiedad_id', $temporada->propiedad_id)->get();

        $auxCapacidad = 0;
        foreach ($tipos_habitacion as $tipo) {
            if ($tipo->capacidad > $auxCapacidad) {
                $auxCapacidad = $tipo->capacidad;
            }
            $tipo_habitacion_id = $tipo->id;
            $tipo_moneda        = TipoMoneda::whereHas('preciosTemporada', function ($query) use ($temporada_id, $tipo_habitacion_id) {
                $query->where('temporada_id', $temporada_id)
                      ->where('tipo_habitacion_id', $tipo_habitacion_id);})
            ->with(['preciosTemporada' => function ($q) use ($temporada_id, $tipo_habitacion_id) {
                $q->where('temporada_id', $temporada_id)
                  ->where('tipo_habitacion_id', $tipo_habitacion_id);}])->get();

            $tipo->tipos_moneda = $tipo_moneda;
        }

        $data['tipos_habitacion'] = $tipos_habitacion;
        if ($propiedad->tipo_cobro_id != 3) {
            $cobro_propiedad = ['Precio'];
            $data['cobro_propiedad'] = $cobro_propiedad;

        } else {

            $cantidad_huespedes = [];
            for ($i=1; $i<=$auxCapacidad ; $i++){
                $cobro_propiedad = "Huésped ". $i;
                array_push($cantidad_huespedes, $cobro_propiedad);
            }
            $data['cobro_propiedad'] = $cantidad_huespedes;
        }

        return $data;
    }

    public function editarTemporadas(Request $request)
    {

        if ($request->has('temporadas')) {

            $temporadas = $request['temporadas'];

            foreach ($temporadas as $temporada) {

                $temp = Temporada::where('id', $temporada['temporada_id'])->first();

                $temp->update(array('color' => $temporada['color']));

            }


            $data = [

                'errors' => false,
                'msg'    => 'Actualizadao',

            ];

            return Response::json($data, 201);

        } else {

            $data = [
                    'errors' => true,
                    'msg'    => 'Solicitud incompleta',

                ];

                return Response::json($data, 400);

        }

    }

    public function destroy($id)
    {

        $temporada = Temporada::findOrFail($id);
        $temporada->delete();

        $data = [

            'errors' => false,
            'msg'    => 'Temporada eliminada satisfactoriamente',

        ];

        return Response::json($data, 202);

    }

}
