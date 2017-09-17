<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\ZonaHoraria;
use App\Caja;
use App\TipoMonto;
use App\MontoCaja;
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



    public function tipoMonto()
    {

        $tipos = TipoMonto::all();
        return $tipos;
    }


}
