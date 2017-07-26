<?php

namespace App\Http\Controllers;

use App\Equipamiento;
use App\Habitacion;
use App\Http\Controllers\Controller;
use App\PrecioTemporada;
use App\Propiedad;
use App\Temporada;
use App\TipoHabitacion;
use App\TipoMoneda;
use App\Calendario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Response;

class HabitacionController extends Controller
{

    public function disponibilidad(Request $request)
    {

        $propiedad_id = $request->input('propiedad_id');
        $fecha_inicio = $request->input('fecha_inicio');
        $fecha_fin    = $request->input('fecha_fin');

        $propiedad = Propiedad::where('id', $propiedad_id)->first();

        if (!is_null($propiedad)) {

            $propiedad_monedas = $propiedad->tipoMonedas; // monedas propiedad

            $fechaInicio = strtotime($fecha_inicio);
            $fechaFin    = strtotime($fecha_fin);
            $inicio      = new Carbon($request->fecha_inicio);
            $fin         = new Carbon($request->fecha_fin);

            if ($fechaInicio < $fechaFin) {

                $habitaciones_ocupadas    = [];
                $habitaciones_disponibles = [];

                $habitaciones_propiedad = Habitacion::where('propiedad_id', $propiedad_id)->with('tipoHabitacion')->get();

                for ($i = $fechaInicio; $i < $fechaFin; $i += 86400) {

                    $fecha = date("Y-m-d", $i);

                    $habitaciones = Habitacion::where('propiedad_id', $request->input('propiedad_id'))->whereHas('reservas', function ($query) use ($fecha) {

                        $query->where('checkin', '<=', $fecha)->where('checkout', '>', $fecha)->where('estado_reserva_id', '!=', 6)->where('estado_reserva_id', '!=', 7);

                    })->with('tipoHabitacion')->get();

                    foreach ($habitaciones as $habitacion) {

                        if (!in_array($habitacion, $habitaciones_ocupadas)) {

                            array_push($habitaciones_ocupadas, $habitacion);

                        }
                    }
                }

                $tipo_habitacion_propiedad = [];
                foreach ($habitaciones_propiedad as $hab) {

                    $tipo_habitacion = $hab->tipoHabitacion;
                    if (!in_array($tipo_habitacion, $tipo_habitacion_propiedad)) {

                        array_push($tipo_habitacion_propiedad,$tipo_habitacion);

                    }

                    if (!in_array($hab, $habitaciones_ocupadas)) {

                        array_push($habitaciones_disponibles, $hab);

                    }


                }

                foreach ($habitaciones_disponibles as $habitacion) {
                    
                    $cantidad_dias      = $fin->diffInDays($inicio); // diferencia de dias entre fechas
                    $tipo_habitacion_id = $habitacion->tipo_habitacion_id;
                    $precios            = $habitacion->tipoHabitacion->precios;

                    $precio_promedio_habitacion = [];
                    $auxPrecio                  = [];
                    $auxFecha                   = new Carbon($inicio);

                    while ($auxFecha < $fin) {

                    $temporada = Temporada::where('propiedad_id', $propiedad_id)->whereHas('calendarios', function ($query) use ($auxFecha) {
                            $query->where('fecha', $auxFecha);})->first();

                    $temporada_id = $temporada->id;

                    $precios_temporada = $precios->where('temporada_id', $temporada_id)->where('tipo_habitacion_id', $habitacion->tipo_habitacion_id);



                    if (count($precios_temporada) == count($propiedad_monedas)) {
                        
                        foreach ($propiedad_monedas as $moneda) {
                            $tipo_moneda = $moneda->id;

                            foreach ($precios_temporada as $precio) {
                                if ($tipo_moneda == $precio->tipo_moneda_id) {

                                    $precio_tipo_habitacion = ['precio' => $precio->precio, 'tipo_moneda_id' => $moneda->id, 'nombre_moneda' => $moneda->nombre, 'cantidad_decimales' => $moneda->cantidad_decimales];

                                    array_push($auxPrecio, $precio_tipo_habitacion);

                                }

                            }
                        }

                    }

                    $auxFecha->addDay();
                    }



                    foreach ($propiedad_monedas as $moneda) {

                        $moneda_id = $moneda->id;

                        $sumaPrecio = 0;
                        foreach ($auxPrecio as $precio_habitacion) {

                            if ($moneda_id == $precio_habitacion['tipo_moneda_id']) {

                                $sumaPrecio += $precio_habitacion['precio'];
                            }

                        }

                    if ($sumaPrecio != 0 ) {
                        
                        $precio_promedio = ['precio' => round($sumaPrecio / $cantidad_dias), 'tipo_moneda_id' => $moneda->id, 'nombre_moneda' => $moneda->nombre, 'cantidad_decimales' => $moneda->cantidad_decimales];

                        array_push($precio_promedio_habitacion, $precio_promedio);

                    }


            }

                $habitacion->precios = $precio_promedio_habitacion; 



                }


                $habitaciones_tipo = [];
                foreach ($tipo_habitacion_propiedad as $tipo) {
                    
                    $habitaciones = [];
                    foreach ($habitaciones_disponibles as $habitacion) {
                        
                        if ($tipo->id == $habitacion->tipo_habitacion_id) {
                            
                                array_push($habitaciones, $habitacion);

                        }

                    }
                    $auxTipo = ['id' => $tipo->id, 'nombre' => $tipo->nombre, 'habitaciones' => $habitaciones];
                    
                    array_push($habitaciones_tipo, $auxTipo);
                    

                }

                $data = ['tipos' => $habitaciones_tipo];

                return $data;

            } else {

                $retorno = array(

                    'msj'    => "Las fechas no corresponden",
                    'errors' => true,
                );

                return Response::json($retorno, 400);

            }

        } else {

            $data = array(

                'msj'    => "Propiedad no encontrada",
                'errors' => true,

            );

            return Response::json($data, 404);

        }

    }


    public function index(Request $request)
    {

        if ($request->has('propiedad_id')) {
            $habitaciones = Habitacion::where('propiedad_id', $request->input('propiedad_id'))->with('estado')->with('tipoHabitacion')->with('equipamiento')->get();
            return $habitaciones;

        }

    }

    public function store(Request $request)
    {

        $rules = array(

            'nombre'              => 'required',
            'piso'                => 'required|numeric',
            'propiedad_id'        => 'required|numeric',
            'tipo_habitacion_id'  => 'required|numeric',
            'bano'                => 'required',
            'tv'                  => 'required',
            'wifi'                => 'required',
            'frigobar'            => 'required',

        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            $data = [

                'errors' => true,
                'msg'    => $validator->messages(),

            ];

            return Response::json($data, 400);

        } else {

            $propiedad_id          = $request->get('propiedad_id');
            $propiedad             = Propiedad::where('id', $propiedad_id)->first();
            $cantidad_habitaciones = $propiedad->numero_habitaciones;

            $habitaciones_ingresadas = $propiedad->habitaciones->count();

            if ($cantidad_habitaciones > $habitaciones_ingresadas) {

                $habitacion                      = new Habitacion();
                $habitacion->nombre              = $request->get('nombre');
                $habitacion->piso                = $request->get('piso');
                $habitacion->propiedad_id        = $request->get('propiedad_id');
                $habitacion->tipo_habitacion_id  = $request->get('tipo_habitacion_id');
                $habitacion->save();

                $equipamiento                = new Equipamiento();
                $equipamiento->bano          = $request->get('bano');
                $equipamiento->tv            = $request->get('tv');
                $equipamiento->wifi          = $request->get('wifi');
                $equipamiento->frigobar      = $request->get('frigobar');
                $equipamiento->habitacion_id = $habitacion->id;
                $equipamiento->save();

                $hab = Habitacion::where('id', $habitacion->id)->first();

                $data = [
                    'errors' => false,
                    'msg'    => 'Habitacion creado satisfactoriamente',

                ];

                return Response::json($data, 201);

            } else {

                $data = [
                    'errors' => true,
                    'msg'    => 'Habitaciones ya creadas',

                ];

                return Response::json($data, 400);

            }

        }

    }

    public function update(Request $request, $id)
    {

        $rules = array(

            'nombre'              => '',
            /* 'precios'               => 'array',*/
            'piso'                => 'numeric',
            'tipo_habitacion_id'  => 'numeric',
            'bano'                => '',
            'tv'                  => '',
            'wifi'                => '',
            'frigobar'            => '',

        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            $data = [

                'errors' => true,
                'msg'    => $validator->messages(),

            ];

            return Response::json($data, 400);

        } else {

            $propiedad = Propiedad::whereHas('habitaciones', function ($query) use ($id) {

                $query->where('id', $id);

            })->first();

            $habitacion = Habitacion::findOrFail($id);
            $habitacion->update($request->all());
            $habitacion->touch();

            $equipamiento = Equipamiento::findOrFail($id);

            $equipamiento->update($request->all());
            $equipamiento->touch();

            $data = [

                'errors' => false,
                'msg'    => 'Habitacion actualizada satisfactoriamente',

            ];

            return Response::json($data, 201);

        }

    }

    public function precioHabitacion(Request $request)
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

        if ($request->has('habitacion_id')) {
            $habitacion_id  = $request->input('habitacion_id');
            $habitacion     = Habitacion::where('id', $habitacion_id)->first();
            if (is_null($habitacion)) {
                $retorno  = array(
                    'msj'    => "Habitacion no encontrada",
                    'errors' => true);
                return Response::json($retorno, 404);
            }
        } else {
            $retorno = array(
                'msj'    => "No se envia habitacion_id",
                'errors' => true);
            return Response::json($retorno, 400);
        }

        if ($request->has('fecha_fin') && $request->has('fecha_inicio')) {
            $fechaInicio   = new Carbon($request->input('fecha_inicio'));
            $fechaFin      = new Carbon($request->input('fecha_fin'));

            $cantidad_dias = $fechaFin->diffInDays($fechaInicio);   // diferencia de dias entre fechas
        }

            $precios                    = $habitacion->tipoHabitacion->precios;
            $tipo_habitacion_id         = $habitacion->tipo_habitacion_id;
            $propiedad_monedas          = $propiedad->tipoMonedas; // monedas propiedad
            $capacidad                  = $habitacion->tipoHabitacion->capacidad;

            $precio_promedio_habitacion = [];
            $auxPrecio                  = [];
            $auxFecha                   = new Carbon($fechaInicio);

            while ($auxFecha < $fechaFin) {

                $temporada = Temporada::where('propiedad_id', $propiedad_id)->whereHas('calendarios', function ($query) use ($auxFecha) {
                    $query->where('fecha', $auxFecha);})->first();

                if (!is_null($temporada)) {

                    $temporada_id      = $temporada->id;
                    $precios_temporada = $precios->where('temporada_id', $temporada_id)->where('tipo_habitacion_id', $habitacion->tipo_habitacion_id);
                    foreach ($precios_temporada as $precio) {
                        if ($precio->precio == 0) {
                            $data = array(
                                'msj'    => "debe configurar precios para este tipo de habitacion",
                                'errors' => true,
                            );
                            return Response::json($data, 400);
                        }
                    }

                    if ($propiedad->tipo_cobro_id != 3) {
                        foreach ($propiedad_monedas as $moneda) {
                            $tipo_moneda = $moneda->id;

                            foreach ($precios_temporada as $precio) {
                                if ($tipo_moneda == $precio->tipo_moneda_id) {

                                    $precio_tipo_habitacion = ['cantidad_huespedes' => $precio->cantidad_huespedes, 'precio' => $precio->precio, 'tipo_moneda_id' => $moneda->id, 'nombre_moneda' => $moneda->nombre, 'cantidad_decimales' => $moneda->cantidad_decimales];

                                    array_push($auxPrecio, $precio_tipo_habitacion);
                                }
                            }
                        }

                    } else {

                        foreach ($propiedad_monedas as $moneda) {

                            $tipo_moneda = $moneda->id;
                            foreach ($precios_temporada as $precio) {
                                if ($tipo_moneda == $precio->tipo_moneda_id) {

                                    $precio_tipo_habitacion = ['cantidad_huespedes' => $precio->cantidad_huespedes, 'precio' => $precio->precio, 'tipo_moneda_id' => $moneda->id, 'nombre_moneda' => $moneda->nombre, 'cantidad_decimales' => $moneda->cantidad_decimales];

                                    array_push($auxPrecio, $precio_tipo_habitacion);
                                }
                            }
                        }
                    }

                } else {

                    $data = array(
                        'msj'    => "Debe configurar una temporada para la fecha " . $auxFecha,
                        'errors' => true,
                    );
                    return Response::json($data, 400);
                }

                $auxFecha->addDay();

            }

            for ($i=1; $i<=$capacidad ; $i++) {

                foreach ($propiedad_monedas as $moneda) {
                    $moneda_id  = $moneda->id;
                    $sumaPrecio = 0;
                    foreach ($auxPrecio as $precio_habitacion) {
                        if ($precio_habitacion['tipo_moneda_id'] == $moneda_id && $precio_habitacion['cantidad_huespedes'] == $i) {
                            $sumaPrecio += $precio_habitacion['precio'];
                        }
                    }

                    $precio_promedio = ['cantidad_huespedes' => $i, 'precio' => ($sumaPrecio / $cantidad_dias), 'tipo_moneda_id' => $moneda->id, 'nombre_moneda' => $moneda->nombre, 'cantidad_decimales' => $moneda->cantidad_decimales];

                    array_push($precio_promedio_habitacion, $precio_promedio);

                }
            }

/*            return $precio_promedio_habitacion;

            foreach ($propiedad_monedas as $moneda) {

                foreach ($precio_promedio_habitacion as $precio) {


                    
                }
            }*/


            $habitacion->precios = $precio_promedio_habitacion;

            return $habitacion;


    }

/*    public function temporada(Request $request)
    {
        if ($request->has('propiedad_id')) {
            $propiedad_id = $request->input('propiedad_id');
            $propiedad    = Propiedad::where('id', $propiedad_id)->first();
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

        if ($request->has('precios')) {
           $precios = $request['precios'];

        } else {

            $retorno = array(
                'msj'    => "No se envia precios",
                'errors' => true);
            return Response::json($retorno, 400);

        }

        switch ($propiedad->tipo_cobro_id) {
            case 1:
                foreach ($precios as $precio) {
                    $precio_temporada                     = new PrecioTemporada;

                    $precio_temporada->cantidad_huespedes = 1;
                    $precio_temporada->precio             = $precio['precio'];
                    $precio_temporada->tipo_habitacion_id = $precio['tipo_habitacion_id'];
                    $precio_temporada->tipo_moneda_id     = $precio['tipo_moneda_id'];
                    $precio_temporada->temporada_id       = $precio['temporada_id'];
                    $precio_temporada->save();
                }
            break;

            case 2:
                foreach ($precios as $precio) {
                    $precio_temporada                     = new PrecioTemporada;

                    $precio_temporada->cantidad_huespedes = 1;
                    $precio_temporada->precio             = $precio['precio'];
                    $precio_temporada->tipo_habitacion_id = $precio['tipo_habitacion_id'];
                    $precio_temporada->tipo_moneda_id     = $precio['tipo_moneda_id'];
                    $precio_temporada->temporada_id       = $precio['temporada_id'];
                    $precio_temporada->save();
                }
            break;

            case 3:
                foreach ($precios as $precio) {
                    $precio_temporada                     = new PrecioTemporada;

                    $precio_temporada->cantidad_huespedes = $precio['cantidad_huespedes'];
                    $precio_temporada->precio             = $precio['precio'];
                    $precio_temporada->tipo_habitacion_id = $precio['tipo_habitacion_id'];
                    $precio_temporada->tipo_moneda_id     = $precio['tipo_moneda_id'];
                    $precio_temporada->temporada_id       = $precio['temporada_id'];
                    $precio_temporada->save();
                }
            break;
        }

            $retorno = array(

                'msj'    => "Precios creados satisfactoriamente",
                'errors' => false,
            );

            return Response::json($retorno, 201);
    }*/


    public function destroy($id)
    {

        $habitaciones = Habitacion::where('id', $id)->whereHas('calendarios', function ($query) {
            $query->where('reservas', 1);})->get();

        if (count($habitaciones) == 0) {

            $habitacion = Habitacion::findOrFail($id);
            $habitacion->delete();

            $data = [

                'errors' => false,
                'msg'    => 'Habitacion eliminada satisfactoriamente',

            ];

            return Response::json($data, 202);

        } elseif (count($habitaciones) == 1) {

            $data = [

                'errors' => true,
                'msg'    => 'Metodo fallido',

            ];

            return Response::json($data, 401);

        }

    }

    public function getTipoHabitacion()
    {

        $tipoHabitacion = TipoHabitacion::all();
        return $tipoHabitacion;

    }

    public function getTipoMoneda()
    {

        $tipoMoneda = TipoMoneda::all();

        return $tipoMoneda;

    }

}
