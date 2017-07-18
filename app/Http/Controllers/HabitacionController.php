<?php

namespace App\Http\Controllers;

use App\Equipamiento;
use App\Habitacion;
use App\Http\Controllers\Controller;
use App\Precio;
use App\PrecioTemporada;
use App\Propiedad;
use App\Temporada;
use App\TipoHabitacion;
use App\TipoMoneda;
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

    /**
     * se obtiene las habitaciones disponibles en un rango de fechas
     *
     * @author ALLEN
     *
     * @param  Request          $request (propiedad_id, fecha_inicio, fecha_fin)
     * @return Response::json
     */

/*    public function Disponibilidad(Request $request){

$fecha_inicio = $request->input('fecha_inicio');
$fecha_fin    = $request->input('fecha_fin');

$rango = [$fecha_inicio, $fecha_fin];

$dias = ((strtotime($fecha_fin)-strtotime($fecha_inicio))/86400)+1;

if($request->has('propiedad_id')){

$habitaciones = Habitacion::where('propiedad_id', $request->input('propiedad_id'))->whereHas('calendarios', function($query) use($rango) {
$query->whereBetween('fecha',  $rango)->where('reservas', 0);}, '=', $dias)->with('tipoHabitacion')->get();

}

$habitacion_individual          = [];
$habitacion_doble               = [];
$habitacion_triple              = [];
$habitacion_cuadruple           = [];
$habitacion_quintuple           = [];
$habitacion_matrimonial         = [];
$habitacion_suite               = [];
$habitacion_presidencial        = [];

foreach ($habitaciones as $habitacion) {

if($habitacion->tipo_habitacion_id == 1){

array_push($habitacion_individual, $habitacion);

}elseif($habitacion->tipo_habitacion_id == 2){

array_push($habitacion_doble, $habitacion);

}elseif($habitacion->tipo_habitacion_id == 3){

array_push($habitacion_triple, $habitacion);

}elseif ($habitacion->tipo_habitacion_id == 4) {

array_push($habitacion_cuadruple, $habitacion);

}elseif ($habitacion->tipo_habitacion_id == 5) {

array_push($habitacion_quintuple, $habitacion);

}elseif ($habitacion->tipo_habitacion_id == 6) {

array_push($habitacion_matrimonial, $habitacion);

}elseif ($habitacion->tipo_habitacion_id == 7) {

array_push($habitacion_suite, $habitacion);

}elseif ($habitacion->tipo_habitacion_id == 8) {

array_push($habitacion_presidencial, $habitacion);
}

}

$habitaciones_tipo = array(
'tipos'           => [
['id' => 1, 'nombre' => 'individual',   'habitaciones' => $habitacion_individual    ],
['id' => 2, 'nombre' => 'doble',        'habitaciones' => $habitacion_doble         ],
['id' => 3, 'nombre' => 'triple',       'habitaciones' => $habitacion_triple        ],
['id' => 4, 'nombre' => 'cuadruple',    'habitaciones' => $habitacion_cuadruple     ],
['id' => 5, 'nombre' => 'quintuple',    'habitaciones' => $habitacion_quintuple     ],
['id' => 6, 'nombre' => 'matrimonial',  'habitaciones' => $habitacion_matrimonial   ],
['id' => 7, 'nombre' => 'suite',        'habitaciones' => $habitacion_suite         ],
['id' => 8, 'nombre' => 'presidencial', 'habitaciones' => $habitacion_presidencial  ],

],

);

return $habitaciones_tipo;

}*/

    public function index(Request $request)
    {

        if ($request->has('propiedad_id')) {
            $habitaciones = Habitacion::where('propiedad_id', $request->input('propiedad_id'))->with('estado')->with('tipoHabitacion')->with('precios.TipoMoneda')->with('equipamiento')->get();
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
           /* $cantidad_habitaciones = $propiedad->numero_habitaciones;*/

/*            $habitaciones_ingresadas = $propiedad->habitaciones->count();

            if ($cantidad_habitaciones > $habitaciones_ingresadas) {*/

                $habitacion                      = new Habitacion();
                $habitacion->nombre              = $request->get('nombre');
                /*$habitacion->disponibilidad_base = $request->get('disponibilidad_base');*/
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

/*            } else {

                $data = [
                    'errors' => true,
                    'msg'    => 'Habitaciones ya creadas',

                ];

                return Response::json($data, 400);

            }*/

        }

    }

    public function update(Request $request, $id)
    {

        $rules = array(

            'nombre'              => '',
            'precio_base'         => 'numeric',
            /* 'precios'               => 'array',*/
            'disponibilidad_base' => 'numeric',
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

        if ($request->has('fecha_fin') && $request->has('fecha_inicio') && $request->has('habitacion_id') && $request->has('propiedad_id')) {

            $habitacion_id = $request->input('habitacion_id');
            $propiedad_id  = $request->input('propiedad_id');

            $propiedad = Propiedad::where('id', $propiedad_id)->first();

            $propiedad_monedas = $propiedad->tipoMonedas; // monedas propiedad

            $habitacion         = Habitacion::where('id', $habitacion_id)->where('propiedad_id', $propiedad_id)->first();
            $tipo_habitacion_id = $habitacion->tipo_habitacion_id;
            $precios            = $habitacion->tipoHabitacion->precios;

            $fechaInicio = new Carbon($request->fecha_inicio);
            $fechaFin    = new Carbon($request->fecha_fin);

            $cantidad_dias = $fechaFin->diffInDays($fechaInicio); // diferencia de dias entre fechas

            $precio_promedio_habitacion = [];
            $auxPrecio                  = [];
            $auxFecha                   = new Carbon($fechaInicio);

            while ($auxFecha < $fechaFin) {

                $temporada = Temporada::where('propiedad_id', $propiedad_id)->whereHas('calendarios', function ($query) use ($auxFecha) {
                    $query->where('fecha', $auxFecha);})->first();

                if (!is_null($temporada)) {

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

                    } else {

                        $data = array(

                            'msj'    => "debe configurar precios para este tipo de habitacion",
                            'errors' => true,

                        );

                        return Response::json($data, 400);

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

            foreach ($propiedad_monedas as $moneda) {

                $moneda_id = $moneda->id;

                $sumaPrecio = 0;
                foreach ($auxPrecio as $precio_habitacion) {

                    if ($moneda_id == $precio_habitacion['tipo_moneda_id']) {

                        $sumaPrecio += $precio_habitacion['precio'];
                    }

                }

                $precio_promedio = ['precio' => round($sumaPrecio / $cantidad_dias), 'tipo_moneda_id' => $moneda->id, 'nombre_moneda' => $moneda->nombre, 'cantidad_decimales' => $moneda->cantidad_decimales];

                array_push($precio_promedio_habitacion, $precio_promedio);

            }

            $habitacion->precios = $precio_promedio_habitacion;

            return $habitacion;

        } else {

            $data = array(

                'msj'    => "solicitud incompleta",
                'errors' => true,

            );

            return Response::json($data, 400);

        }

    }

    public function temporada(Request $request)
    {

        if ($request->has('precios')) {

            foreach ($request['precios'] as $precio) {

                $precio_tipo_habitacion = PrecioTemporada::where('tipo_habitacion_id', $precio['tipo_habitacion_id'])->where('tipo_moneda_id', $precio['tipo_moneda_id'])->where('temporada_id', $precio['temporada_id'])->first();

                if (is_null($precio_tipo_habitacion)) {

                    $precio_temporada = new PrecioTemporada;

                    $precio_temporada->precio             = $precio['precio'];
                    $precio_temporada->tipo_habitacion_id = $precio['tipo_habitacion_id'];
                    $precio_temporada->tipo_moneda_id     = $precio['tipo_moneda_id'];
                    $precio_temporada->temporada_id       = $precio['temporada_id'];
                    $precio_temporada->save();

                } else {

                    $precio_temporada = $precio['precio'];

                    $precio_tipo_habitacion->update(array('precio' => $precio_temporada));

                }

            }

            $data = array(

                'msj'    => "Precios guardados",
                'errors' => false,

            );

            return Response::json($data, 201);

        } else {

            $data = array(

                'msj'    => "No se envia precios",
                'errors' => true,

            );

            return Response::json($data, 400);

        }

    }

    public function crearPrecio(Request $request)
    {

        $habitacion_id = $request->input('habitacion_id');

        $propiedad = Propiedad::whereHas('habitaciones', function ($query) use ($habitacion_id) {

            $query->where('id', $habitacion_id);

        })->first();

        $habitacion = Habitacion::where('id', $habitacion_id)->first();

        $tipo_moneda_id = $request->input('tipo_moneda_id');

        $precio_habitacion = $request->input('precio_habitacion');

        $precio                    = new Precio();
        $precio->precio_habitacion = $precio_habitacion;
        $precio->tipo_moneda_id    = $tipo_moneda_id;
        $precio->habitacion_id     = $habitacion_id;
        $precio->save();

        if (count($habitacion->precios) == count($propiedad->tipoMonedas)) {
            $habitacion->update(array('estado_habitacion_id' => 1));

        } else {

            $habitacion->update(array('estado_habitacion_id' => 2));

        }

        $data = [
            'errors' => false,
            'msg'    => 'Precio creado satisfactoriamente',

        ];

        return Response::json($data, 201);

    }


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
