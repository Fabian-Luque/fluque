<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Propiedad;
use App\Temporada;
use App\TipoHabitacion;
use App\TipoMoneda;
use App\Calendario;
use App\Reserva;
use App\Habitacion;
use Response;
use Validator;
use \Carbon\Carbon;

class MotorController extends Controller
{
	public function getDisponibilidad(Request $request)
	{
		if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
			$fecha_inicio = new Carbon($request->input('fecha_inicio'));
			$fecha_fin    = new Carbon($request->input('fecha_fin'));
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

        $inicio = $fecha_inicio->format('Y-m-d');
        $fin    = $fecha_fin->format('Y-m-d');

        $habitaciones = Habitacion::where('propiedad_id', $request->input('propiedad_id'))
        ->whereDoesntHave('reservas', function ($query) use ($inicio, $fin) {
            $query->where(function ($query) use ($inicio, $fin) {
                $query->where(function ($query) use ($inicio, $fin) {
                    $query->where('checkin', '>=', $inicio);
                    $query->where('checkin', '<',  $fin);
                });
                $query->orWhere(function ($query) use ($inicio, $fin) {
                    $query->where('checkout', '>', $inicio);
                    $query->where('checkout', '<=',  $fin);
                });
            });
        })
        ->with('tipoHabitacion')
        ->get();

        $tipos_habitacion = [];
        foreach ($propiedad->tiposHabitacion as $tipo) {
            $cantidad_disponibles = 0;
            foreach ($habitaciones as $habitacion) {
                if ($habitacion->tipo_habitacion_id == $tipo->id) {
                    $cantidad_disponibles += 1;
                }
            }
            if ($cantidad_disponibles > 0) {
                $tipo->cantidad_disponible = $cantidad_disponibles;
            }
            array_push($tipos_habitacion, $tipo);
        }

        $fechaInicio            = new Carbon($request->input('fecha_inicio'));
        $fechaFin               = new Carbon($request->input('fecha_fin'));
        $propiedad_monedas      = $propiedad->tipoMonedas; // monedas propiedad

        foreach ($tipos_habitacion as $tipo) {
            $precios                    = $tipo->precios;
            $tipo_habitacion_id         = $tipo->id;
            $capacidad                  = $tipo->capacidad;
            $precio_promedio_habitacion = [];
            $auxPrecio                  = [];
            $auxFecha                   = new Carbon($fechaInicio);

            while ($auxFecha < $fechaFin) {

                $temporada = Temporada::where('propiedad_id', $propiedad_id)
                ->whereHas('calendarios', function ($query) use ($auxFecha) {
                    $query->where('fecha', $auxFecha);})
                ->first();

                if (!is_null($temporada)) {
                    $temporada_id      = $temporada->id;
                    $precios_temporada = $precios->where('temporada_id', $temporada_id)->where('tipo_habitacion_id', $tipo_habitacion_id);
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
                        'msj'    => "No disponible",
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
            $tipo->precios = $precio_promedio_habitacion;

        }
        return $tipos_habitacion;

    }

}