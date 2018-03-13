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
use App\Reserva;
use App\Bloqueo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Response;

class HabitacionController extends Controller
{

    public function disponibilidad(Request $request)
    {
        if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
            $inicio = new Carbon($request->input('fecha_inicio'));
            $fin    = new Carbon($request->input('fecha_fin'));
        } else {
            $data = array(
                'msj'    => "Propiedad no encontrada",
                'errors' => true,);
            return Response::json($data, 404);
        }

        if ($request->has('propiedad_id')) {
            $propiedad_id   = $request->input('propiedad_id');
            $propiedad      = Propiedad::where('id', $propiedad_id)->with('tiposHabitacion')->first();
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

        $propiedad_monedas         = $propiedad->tipoMonedas; // monedas propiedad
        $tipo_habitacion_propiedad = $propiedad->tiposHabitacion;

        if ($inicio < $fin) {
            
            $fecha_inicio = $inicio->startOfDay()->format('Y-m-d');
            $fecha_fin    = $fin->startOfDay()->format('Y-m-d');

            $habitaciones_disponibles = Habitacion::where('propiedad_id', $request->input('propiedad_id'))
            ->whereDoesntHave('reservas', function ($query) use ($fecha_inicio, $fecha_fin) {
                $query->whereIn('estado_reserva_id', [1,2,3,4,5])
                ->where(function ($query) use ($fecha_inicio, $fecha_fin) {
                    $query->where(function ($query) use ($fecha_inicio, $fecha_fin) {
                        $query->where('checkin', '>=', $fecha_inicio);
                        $query->where('checkin', '<',  $fecha_fin);
                    });
                    $query->orWhere(function($query) use ($fecha_inicio,$fecha_fin){
                        $query->where('checkin', '<=', $fecha_inicio);
                        $query->where('checkout', '>',  $fecha_inicio);
                    });                
                });
            })
            ->with('tipoHabitacion')
            ->get();

            $fechaInicio            = new Carbon($request->input('fecha_inicio'));
            $fechaFin               = new Carbon($request->input('fecha_fin'));
            $propiedad_monedas      = $propiedad->tipoMonedas; // monedas propiedad

            foreach ($habitaciones_disponibles as $habitacion) {
                $precios                    = $habitacion->tipoHabitacion->precios;
                $tipo_habitacion_id         = $habitacion->tipo_habitacion_id;
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
                        if ($propiedad->tipo_cobro_id != 3) {
                            foreach ($propiedad_monedas as $moneda) {
                                $tipo_moneda = $moneda->id;
                                foreach ($precios_temporada as $precio) {
                                    if ($tipo_moneda == $precio->tipo_moneda_id) {
                                        $precio_tipo_habitacion['cantidad_huespedes'] = $precio->cantidad_huespedes;
                                        $precio_tipo_habitacion['precio']             = $precio->precio;
                                        $precio_tipo_habitacion['tipo_moneda_id']     = $moneda->id;
                                        $precio_tipo_habitacion['nombre_moneda']      = $moneda->nombre;
                                        $precio_tipo_habitacion['cantidad_decimales'] = $moneda->cantidad_decimales;
                                        array_push($auxPrecio, $precio_tipo_habitacion);
                                    }
                                }
                            }
                        } else {
                            foreach ($propiedad_monedas as $moneda) {
                                $tipo_moneda = $moneda->id;
                                foreach ($precios_temporada as $precio) {
                                    if ($tipo_moneda == $precio->tipo_moneda_id) {
                                        $precio_tipo_habitacion['cantidad_huespedes'] = $precio->cantidad_huespedes;
                                        $precio_tipo_habitacion['precio']             = $precio->precio;
                                        $precio_tipo_habitacion['tipo_moneda_id']     = $moneda->id;
                                        $precio_tipo_habitacion['nombre_moneda']      = $moneda->nombre;
                                        $precio_tipo_habitacion['cantidad_decimales'] = $moneda->cantidad_decimales;
                                        array_push($auxPrecio, $precio_tipo_habitacion);
                                    }
                                }
                            }
                        }
                    } else {
                        $data = array(
                            'msj'    => "Debe configurar una temporada para las fechas seleccionadas ",
                            'errors' => true,);
                        return Response::json($data, 400);
                    }
                    $auxFecha->addDay();

                }

                if ($propiedad->tipo_cobro_id != 3) {
                    foreach ($propiedad_monedas as $moneda) {
                        $moneda_id  = $moneda->id;
                        $sumaPrecio = 0;
                        foreach ($auxPrecio as $precio_habitacion) {
                            if ($precio_habitacion['tipo_moneda_id'] == $moneda_id && $precio_habitacion['cantidad_huespedes'] == 1) {
                                $sumaPrecio += $precio_habitacion['precio'];
                            }
                        }
                        $precio_promedio['cantidad_huespedes'] = 1;
                        $precio_promedio['precio']             = $sumaPrecio;
                        $precio_promedio['tipo_moneda_id']     = $moneda->id;
                        $precio_promedio['nombre_moneda']      = $moneda->nombre;
                        $precio_promedio['cantidad_decimales'] = $moneda->cantidad_decimales;
                        array_push($precio_promedio_habitacion, $precio_promedio);
                    }
                } else {
                    for ($i=1; $i<=$capacidad ; $i++) {
                        foreach ($propiedad_monedas as $moneda) {
                            $moneda_id  = $moneda->id;
                            $sumaPrecio = 0;
                            foreach ($auxPrecio as $precio_habitacion) {
                                if ($precio_habitacion['tipo_moneda_id'] == $moneda_id && $precio_habitacion['cantidad_huespedes'] == $i) {
                                    $sumaPrecio += $precio_habitacion['precio'];
                                }
                            }
                            $precio_promedio['cantidad_huespedes'] = $i;
                            $precio_promedio['precio']             = $sumaPrecio;
                            $precio_promedio['tipo_moneda_id']     = $moneda->id;
                            $precio_promedio['nombre_moneda']      = $moneda->nombre;
                            $precio_promedio['cantidad_decimales'] = $moneda->cantidad_decimales;
                            array_push($precio_promedio_habitacion, $precio_promedio);
                        }
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

            $data = ['tipos_habitaciones' => $habitaciones_tipo];
            return $data;

        } else {
            $retorno = array(
                'msj'    => "Las fechas no corresponden",
                'errors' => true,);
            return Response::json($retorno, 400);
        }

    }

    public function bloqueoHabitacion(Request $request)
    {
        if ($request->has('habitacion_id')) {
            $habitacion_id   = $request->input('habitacion_id');
            $habitacion      = Habitacion::where('id', $habitacion_id)->first();
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

        if ($request->has('fecha_inicio') && $request->has('fecha_fin') && $request->has('noches')) {

            $fecha_inicio = $request->fecha_inicio;
            $fecha_fin    = $request->fecha_fin;
            $propiedad_id = $habitacion->propiedad_id;

            $reservas = Reserva::whereHas('habitacion', function($query) use($propiedad_id){
                $query->where('propiedad_id', $propiedad_id);
            })
            ->where('habitacion_id', $habitacion_id)
            ->where('checkin', '>' , $fecha_inicio)
            ->where('checkin', '<' ,$fecha_fin)
            ->whereIn('estado_reserva_id', [1,2,3,4,5])
            ->first();

            if (is_null($reservas)) {
                $bloqueo                = new Bloqueo();
                $bloqueo->fecha_inicio  = $request->fecha_inicio;
                $bloqueo->fecha_fin     = $request->fecha_fin;
                $bloqueo->noches        = $request->noches;
                $bloqueo->habitacion_id = $habitacion->id;
                $bloqueo->save();
                
            } else {
                $retorno = array(
                    'msj'    => "No permitido",
                    'errors' => true);
                return Response::json($retorno, 400);
            }
 
            $retorno['errors'] = false;
            $retorno['msj']    = "Habitacion bloqueda satisfactoriamente";
            return Response::json($retorno, 201);

        } else {

            $retorno['errors'] = false;
            $retorno['msj']    = "Incompleto";
            return Response::json($retorno, 201);
        }

    }

    public function eliminarBloqueoHabitacion(Request $request)
    {
        if ($request->has('bloqueo_id')) {
            $bloqueo_id   = $request->input('bloqueo_id');
            $bloqueo      = Bloqueo::where('id', $bloqueo_id)->first();
            if (is_null($bloqueo)) {
                $retorno  = array(
                    'msj'    => "No encontrado",
                    'errors' => true);
                return Response::json($retorno, 404);
            }
        } else {
            $retorno = array(
                'msj'    => "No se envia bloqueo_id",
                'errors' => true);
            return Response::json($retorno, 400);
        }

        $bloqueo->delete();

        $retorno['error'] = false;
        $retorno['msj']   = 'Habitación desbloqueada';
        return Response::json($retorno, 202);

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
            'propiedad_id'        => 'required|numeric',
            'tipo_habitacion_id'  => 'required|numeric',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $data = [
                'errors' => true,
                'msg'    => $validator->messages(),];
            return Response::json($data, 400);

        } else {
            $propiedad_id            = $request->get('propiedad_id');
            $propiedad               = Propiedad::where('id', $propiedad_id)->first();
            $cantidad_habitaciones   = $propiedad->numero_habitaciones;
            $habitaciones_ingresadas = $propiedad->habitaciones->count();

            if ($cantidad_habitaciones > $habitaciones_ingresadas) {
                $habitacion                      = new Habitacion();
                $habitacion->nombre              = $request->get('nombre');
                $habitacion->propiedad_id        = $request->get('propiedad_id');
                $habitacion->tipo_habitacion_id  = $request->get('tipo_habitacion_id');
                $habitacion->save();

                $tipo_habitacion = TipoHabitacion::where('id', $request->get('tipo_habitacion_id'))->first();
                $cantidad = $tipo_habitacion->cantidad;

                $tipo_habitacion->update(array('cantidad' => $cantidad + 1));

                $data = [
                    'errors' => false,
                    'msg'    => 'Habitacion creado satisfactoriamente',];
                return Response::json($data, 201);

            } else {
                $data = [
                    'errors' => true,
                    'msg'    => 'Habitaciones ya creadas',];
                return Response::json($data, 400);
            }
        }

    }

    public function update(Request $request, $id)
    {
        $rules = array(

            'nombre'              => '',
            'tipo_habitacion_id'  => 'numeric',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            $data = [
                'errors' => true,
                'msg'    => $validator->messages(),
            ];
            return Response::json($data, 400);

        } else {

            $habitacion       = Habitacion::findOrFail($id);
            $tipo_habitacion  = TipoHabitacion::where('id', $habitacion->tipo_habitacion_id)->first();
            $cantidad         = $tipo_habitacion->cantidad;
            $disponible_venta = $tipo_habitacion->disponible_venta;
            $tipo_habitacion->update(array('cantidad' => $cantidad - 1));
            if ($cantidad - 1 < $disponible_venta) {
                $tipo_habitacion->update(array('disponible_venta' => $tipo_habitacion->disponible_venta - 1));
            }
            $habitacion->update($request->all());
            $habitacion->touch();

            $tipo_hab        = TipoHabitacion::where('id', $request->input('tipo_habitacion_id'))->first();
            $cant            = $tipo_hab->cantidad;
            $tipo_hab->update(array('cantidad' => $cant + 1));

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
        }


        $fecha_checkin  = $fechaInicio->format('Y-m-d');
        $fecha_checkout = $fechaFin->format('Y-m-d');
        $fechas         = [$fecha_checkin, $fecha_checkout]; 

        $reservas = Reserva::whereHas('habitacion', function($query) use($propiedad_id){
                $query->where('propiedad_id', $propiedad_id);
        })->where('habitacion_id', $habitacion_id)->where('checkin', '>' , $fecha_checkin)->where('checkin', '<' ,$fecha_checkout)->whereIn('estado_reserva_id', [1,2,3,4,5])->get();

        if (count($reservas) != 0 ) {
            $data = array(
                'msj'    => "Ya existen reservas dentro de las fechas seleccionadas",
                'errors' => true,
                    );
            return Response::json($data, 400);
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

        if ($propiedad->tipo_cobro_id != 3) {

            foreach ($propiedad_monedas as $moneda) {
                $moneda_id  = $moneda->id;
                $sumaPrecio = 0;
                foreach ($auxPrecio as $precio_habitacion) {
                    if ($precio_habitacion['tipo_moneda_id'] == $moneda_id && $precio_habitacion['cantidad_huespedes'] == 1) {
                        $sumaPrecio += $precio_habitacion['precio'];
                    }
                }

                $precio_promedio = ['cantidad_huespedes' => 1, 'precio' => $sumaPrecio, 'tipo_moneda_id' => $moneda->id, 'nombre_moneda' => $moneda->nombre, 'cantidad_decimales' => $moneda->cantidad_decimales];

                array_push($precio_promedio_habitacion, $precio_promedio);

            }
            
        } else {

            for ($i=1; $i<=$capacidad ; $i++) {
                foreach ($propiedad_monedas as $moneda) {
                    $moneda_id  = $moneda->id;
                    $sumaPrecio = 0;
                    foreach ($auxPrecio as $precio_habitacion) {
                        if ($precio_habitacion['tipo_moneda_id'] == $moneda_id && $precio_habitacion['cantidad_huespedes'] == $i) {
                            $sumaPrecio += $precio_habitacion['precio'];
                        }
                    }

                    $precio_promedio = ['cantidad_huespedes' => $i, 'precio' => $sumaPrecio, 'tipo_moneda_id' => $moneda->id, 'nombre_moneda' => $moneda->nombre, 'cantidad_decimales' => $moneda->cantidad_decimales];

                    array_push($precio_promedio_habitacion, $precio_promedio);

                }
            }
        }

        $habitacion->precios = $precio_promedio_habitacion;

        return $habitacion;

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
                'msg'    => 'Habitacion eliminada satisfactoriamente',];
            return Response::json($data, 202);

        } elseif (count($habitaciones) == 1) {
            $data = [
                'errors' => true,
                'msg'    => 'Metodo fallido',];
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
