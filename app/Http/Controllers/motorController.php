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

    return $tipos_habitacion;





    }

}