<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\ZonaHoraria;
use App\Caja;
use App\TipoMonto;
use App\MontoCaja;
use App\Propiedad;
use JWTAuth;
use \Carbon\Carbon;
use Response;
use Validator;



class CajaController extends Controller
{
    public function getCaja(Request $request)
    {
        if ($request->has('caja_id')) {
            $caja_id = $request->input('caja_id');
            $caja    = Caja::where('id', $caja_id)->first();
            if (is_null($caja)) {
                $retorno = array(
                    'msj'    => "Caja no encontrada",
                    'errors' => true);
                return Response::json($retorno, 404);
            }
        } else {
            $retorno = array(
                'msj'    => "No se envia caja_id",
                'errors' => true);
            return Response::json($retorno, 400);
        }

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

        $caja  = Caja::where('id', $caja_id)->with('montos.tipoMonto', 'montos.tipoMoneda')->with('user')->with('estadoCaja')->with('pagos.tipoComprobante','pagos.metodoPago', 'pagos.tipoMoneda', 'pagos.reserva')->with('egresosCaja.tipoMoneda', 'egresosCaja.egreso')->first();

        if (!is_null($caja)) {
            $monedas = [];
            foreach ($propiedad->tipoMonedas as $tipo_moneda) {
                $ingreso = 0;
                $egreso  = 0;
                foreach ($caja->pagos as $pago) {
                    if ($tipo_moneda->id == $pago->tipo_moneda_id) {
                        $ingreso += $pago->monto_equivalente;
                    }
                }
                foreach ($caja->egresosCaja as $egreso_caja) {
                    if ($tipo_moneda->id == $egreso_caja->tipo_moneda_id) {
                        $egreso += $egreso_caja->monto;
                    }
                }

                $moneda['nombre']               = $tipo_moneda->nombre;
                $moneda['cantidad_decimales']   = $tipo_moneda->cantidad_decimales;
                $moneda['ingreso']              = $ingreso;
                $moneda['egreso']               = $egreso;
                $moneda['grafico']              = [['parametro' => 'Ingreso', 'valor' => $ingreso], ['parametro' => 'Egreso', 'valor' => $egreso]];
                array_push($monedas, $moneda);
            }
            $data['caja']     = $caja;
            $data['monedas']  = $monedas;

            return $data;

        }
    }

    public function getCajas(Request $request)
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

        if ($request->has('fecha_inicio')) {
            $fecha_inicio = new Carbon($request->get('fecha_inicio'));
            $fecha_fin    = new Carbon($request->get('fecha_inicio'));
            $fin          = $fecha_fin->endOfDay();
        } else {
            $retorno = array(
                'msj'    => "Solicitud incompleta",
                'errors' => true);
            return Response::json($retorno, 400);
        }

        $cajas = Caja::select('cajas.id', 'cajas.fecha_apertura', 'cajas.fecha_cierre' ,'estado_caja.nombre as estado', 'users.name as nombre_usuario')
        ->join('estado_caja', 'estado_caja.id', '=' , 'cajas.estado_caja_id')
        ->join('users', 'users.id', '=' , 'cajas.user_id')
        ->where('propiedad_id', $propiedad_id)
        ->where('fecha_apertura', '>=' , $fecha_inicio)
        ->where('fecha_apertura', '<=' , $fin)
        ->with('montos.tipoMonto', 'montos.tipoMoneda')
        ->with('egresosCaja.tipoMoneda', 'egresosCaja.egreso')
        ->get();

        return $cajas;

    }
    
	public function abrirCaja(Request $request)
	{
        if ($request->has('montos')) {
            $montos = $request->get('montos');
        } else {
            $retorno = array(
                'msj'    => "No se envia montos",
                'errors' => true);
            return Response::json($retorno, 400);
        }

        $user            = JWTAuth::parseToken()->toUser();
        $propiedad       = $user->propiedad[0];
        $zona_horaria_id = $propiedad->zona_horaria_id;
        $zona_horaria    = ZonaHoraria::where('id', $zona_horaria_id)->first();
        $pais            = $zona_horaria['nombre'];
        $fecha_servidor  = Carbon::now();
        $fecha_actual    = $fecha_servidor->tz($pais);

        $caja_abierta    = Caja::where('propiedad_id', $propiedad->id)->where('estado_caja_id', 1)->first();

        if (is_null($caja_abierta)) {
            $caja                   = new Caja();
            $caja->fecha_apertura   = $fecha_actual;
            $caja->user_id          = $user->id;
            $caja->propiedad_id     = $propiedad->id; 
            $caja->estado_caja_id   = 1;
            $caja->save();

            foreach ($montos as $apertura) {
                $monto_apertura                 = new MontoCaja();
                $monto_apertura->monto          = $apertura['monto'];
                $monto_apertura->caja_id        = $caja->id;
                $monto_apertura->tipo_monto_id  = $apertura['tipo_monto_id'];
                $monto_apertura->tipo_moneda_id = $apertura['tipo_moneda_id'];
                $monto_apertura->save();
            }

        } else {
            $retorno = array(
                'msj'    => "Apertura de caja no permitido",
                'errors' => true);
            return Response::json($retorno, 400);
        }

        $retorno = [
            'errors' => false,
            'msj'    => 'Caja abierta satisfactoriamente',
        ];

        return Response::json($retorno, 201);

	}

    public function getCajaAbierta(Request $request)
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

        $caja_abierta  = Caja::where('propiedad_id', $propiedad_id)->where('estado_caja_id', 1)->with('montos.tipoMonto', 'montos.tipoMoneda')->with('user')->with('estadoCaja')->with('pagos.tipoComprobante','pagos.metodoPago', 'pagos.tipoMoneda', 'pagos.reserva')->with('egresosCaja.tipoMoneda', 'egresosCaja.egreso')->first();

        if (!is_null($caja_abierta)) {
            $monedas = [];
            foreach ($propiedad->tipoMonedas as $tipo_moneda) {
                $ingreso = 0;
                $egreso  = 0;
                foreach ($caja_abierta->pagos as $pago) {
                    if ($tipo_moneda->id == $pago->tipo_moneda_id) {
                        $ingreso += $pago->monto_equivalente;
                    }
                }
                foreach ($caja_abierta->egresosCaja as $egreso_caja) {
                    if ($tipo_moneda->id == $egreso_caja->tipo_moneda_id) {
                        $egreso += $egreso_caja->monto;
                    }
                }

                $moneda['nombre']               = $tipo_moneda->nombre;
                $moneda['cantidad_decimales']   = $tipo_moneda->cantidad_decimales;
                $moneda['ingreso']              = $ingreso;
                $moneda['egreso']               = $egreso;
                $moneda['grafico']              = [['parametro' => 'Ingreso', 'valor' => $ingreso], ['parametro' => 'Egreso', 'valor' => $egreso]];
                array_push($monedas, $moneda);
            }
            $data['caja_abierta'] = $caja_abierta;
            $data['monedas']      = $monedas;

            return $data;

        } else {
            $retorno = array(
                'msj'    => "No hay caja abierta",
                'errors' => true);
            return Response::json($retorno, 400);
        }

    }

    public function cerrarCaja(Request $request)
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

        $user            = JWTAuth::parseToken()->toUser();
        $propiedad       = $user->propiedad[0];
        $zona_horaria_id = $propiedad->zona_horaria_id;
        $zona_horaria    = ZonaHoraria::where('id', $zona_horaria_id)->first();
        $pais            = $zona_horaria['nombre'];
        $fecha_servidor  = Carbon::now();
        $fecha_actual    = $fecha_servidor->tz($pais);

        $caja_abierta  = Caja::where('propiedad_id', $propiedad_id)->where('estado_caja_id', 1)->with('montos')->with('user')->with('estadoCaja')->with('pagos')->with('egresosCaja')->first();

        if (!is_null($caja_abierta)) {

            foreach ($propiedad->tipoMonedas as $tipo_moneda) {
                $ingreso = 0;
                $egreso  = 0;
                foreach ($caja_abierta->pagos as $pago) {
                    if ($tipo_moneda->id == $pago->tipo_moneda_id) {
                        $ingreso += $pago->monto_equivalente;
                    }
                }
                foreach ($caja_abierta->egresosCaja as $egreso_caja) {
                    if ($tipo_moneda->id == $egreso_caja->tipo_moneda_id) {
                        $egreso += $egreso_caja->monto;
                    }
                }

                foreach ($caja_abierta->montos as $monto) {
                    if ($monto->tipo_moneda_id == $tipo_moneda->id) {
                        $ingreso  += $monto->monto;
                    }
                }
                $monto_total  = $ingreso - $egreso;

                $monto_cierre                 = new MontoCaja();
                $monto_cierre->monto          = $monto_total;
                $monto_cierre->caja_id        = $caja_abierta->id;
                $monto_cierre->tipo_monto_id  = 2;
                $monto_cierre->tipo_moneda_id = $tipo_moneda->id;
                $monto_cierre->save();

            }

            $caja_abierta->update(array('estado_caja_id' => 2, 'fecha_cierre' => $fecha_actual));

            $retorno = array(
                'errors' => false,
                'msj'    => 'Caja cerrada satisfactoriamente',);
            return Response::json($retorno, 201);

        } else {
            $retorno = array(
                'msj'    => "No hay caja abierta",
                'errors' => true);
            return Response::json($retorno, 400);
        }


    }



    public function tipoMonto()
    {

        $tipos = TipoMonto::all();
        return $tipos;
    }


}
