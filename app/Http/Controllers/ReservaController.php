<?php

namespace App\Http\Controllers;

use App\Calendario;
use App\Cliente;
use App\Habitacion;
use App\Http\Controllers\Controller;
use App\Reserva;
use Illuminate\Http\Request;
use Response;
use \Carbon\Carbon;

class ReservaController extends Controller
{

    /**
     * realizar reservas para un cliente, de una o mas habitaciones
     *
     * @author ALLEN
     *
     * @param  Request          $request ()
     * @return Response::json
     */

    public function reserva(Request $request)
    {

        $clientes = $request['cliente'];

        $habitaciones_info = $request['habitacion_info'];

        if (!is_array($habitaciones_info)) {
            $habitaciones_info = [];
            $habitaciones_info . push($request['habitacion_info']);
        }

        $fecha_inicio = $request->input('fecha_inicio');
        $fecha_fin    = $request->input('fecha_fin');

        if ($clientes['tipo'] == 'particular') {

            $cliente = Cliente::firstOrNew($request['cliente']);

            $cliente->rut       = $clientes['rut'];
            $cliente->tipo      = $clientes['tipo'];
            $cliente->direccion = $clientes['direccion'];
            $cliente->ciudad    = $clientes['ciudad'];
            $cliente->pais      = $clientes['pais'];
            $cliente->telefono  = $clientes['telefono'];
            $cliente->giro      = null;
            $cliente->save();

        } else {

            if ($clientes['tipo'] == 'empresa') {

                $cliente = Cliente::firstOrNew($request['cliente']);

                $cliente->rut       = $clientes['rut'];
                $cliente->tipo      = $clientes['tipo'];
                $cliente->direccion = $clientes['direccion'];
                $cliente->ciudad    = $clientes['ciudad'];
                $cliente->pais      = $clientes['pais'];
                $cliente->telefono  = $clientes['telefono'];
                $cliente->giro      = $clientes['giro'];
                $cliente->save();
            }

        }

        foreach ($habitaciones_info as $habitacion_info) {

            $reserva                 = new Reserva();
            $reserva->monto_total    = $habitacion_info['monto_total'];
            $reserva->monto_sugerido = $habitacion_info['monto_sugerido'];
            $reserva->metodo_pago    = $request['metodo_pago'];
            $reserva->ocupacion      = $habitacion_info['ocupacion'];
            $reserva->fuente         = $request['fuente'];
            $reserva->habitacion_id  = $habitacion_info['id'];
            $reserva->cliente_id     = $cliente->id;
            $reserva->checkin        = $fecha_inicio;
            $reserva->checkout       = $fecha_fin;
            $reserva->estado         = $request['estado'];
            $reserva->noches         = $request['noches'];
            $reserva->save();

            $fecha = $fecha_inicio;

            while (strtotime($fecha) < strtotime($fecha_fin)) {

                $calendario = Calendario::where('fecha', '=', $fecha)->where('habitacion_id', '=', $habitacion_info['id'])->first();

                $calendario->disponibilidad--;
                $calendario->reservas++;
                $calendario->save();

                $fecha = date("Y-m-d", strtotime("+1 day", strtotime($fecha)));
            }
        }

        return 'Habitacion reservada satisfactoriamente';
    }

    public function index(Request $request)
    {
        if ($request->has('fecha_inicio') && $request->has('fecha_fin') && $request->has('propiedad_id')) {
            try {
                $fechaInicio = new Carbon($request->fecha_inicio);
                $fechaFin    = new Carbon($request->fecha_fin);
            } catch (\Exception $e) {
                $return['status']  = 'error';
                $return['message'] = 'Las fechas no corresponden';
                return response()
                    ->json($return)
                    ->header('Access-Control-Allow-Origin', '*');
            }

            if ($fechaInicio < $fechaFin) {
                $fechas       = [$fechaInicio, $fechaFin];
                $habitaciones = Habitacion::where('propiedad_id', $request->propiedad_id)
                    ->where(function ($query) use ($fechas) {
                        $query->whereHas('reservas', function ($query) use ($fechas) {
                            $query->whereBetween('checkin', $fechas);
                        });
                        $query->orWhereHas('reservas', function ($query) use ($fechas) {
                            $query->whereNotBetween('checkin', $fechas);
                        });
                        $query->orHas('reservas', '=', 0);
                    })->with('reservas.cliente')->get();

                foreach ($habitaciones as $habitacion) {
                    $dias     = array();
                    $auxFecha = new Carbon($fechaInicio);
                    while ($auxFecha <= $fechaFin) {
                        $dia             = new \stdClass();
                        $dia->reserva_id = null;
                        foreach ($habitacion->reservas as $reserva) {
                            if (new Carbon($reserva->checkin) <= $auxFecha && $auxFecha < new Carbon($reserva->checkout)) {
                                $dia->reserva_id = $reserva->id;
                            }
                        }
                        $dia->fecha = $auxFecha->toDateString();
                        $dias[]     = $dia;
                        $auxFecha->addDay();
                    }
                    $habitacion->dias = $dias;
                }
                $return['habitaciones'] = $habitaciones;
                $return['status']       = 'ok';
            } else {
                $return['status']  = 'error';
                $return['message'] = 'Las fechas no corresponden';
            }
        } else {
            $return['status']  = 'error';
            $return['message'] = 'La solicitud está incompleta.';
        }

        return response()
            ->json($return)
            ->header('Access-Control-Allow-Origin', '*');
    }

}

/*          $noche = new DetalleNoche();
$noche->precio            = $calendario->precio;
$noche->fecha             = $fecha;
$noche->habitacion_id     = $habitacion_info['id'];
$noche->reserva_id        = $reserva->id;
$noche->save();*/
