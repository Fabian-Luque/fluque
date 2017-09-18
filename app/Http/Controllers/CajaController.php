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
        $fecha_actual    = Carbon::now();
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

        $caja_abierta  = Caja::where('propiedad_id', $propiedad_id)->where('estado_caja_id', 1)->with('user')->with('estadoCaja')->with('pagos')->first();

        if (!is_null($caja_abierta)) {
            $monedas = [];
            foreach ($propiedad->tipoMonedas as $tipo_moneda) {
                $ingreso = 0;
                foreach ($caja_abierta->pagos as $pago) {
                    if ($tipo_moneda->id == $pago->tipo_moneda_id) {
                        $ingreso += $pago->monto_equivalente;
                    }
                }
                $moneda['nombre']               = $tipo_moneda->nombre;
                $moneda['cantidad_decimales']   = $tipo_moneda->cantidad_decimales;
                $moneda['ingreso']              = $ingreso;
                $moneda['grafico']              = ['parametro' => 'Ingreso', 'valor' => $ingreso];
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



    public function tipoMonto()
    {

        $tipos = TipoMonto::all();
        return $tipos;
    }


}
