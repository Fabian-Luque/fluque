<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Response;
use ctala\transaccion\classes\Transaccion;
use ctala\transaccion\classes\Response as Resp;
use Webpatser\Uuid\Uuid;
use App\Events\PagoFacilEvent;
use Illuminate\Support\Facades\Event;
use GuzzleHttp\Client;
use App\Propiedad;
use App\PagoFacil;
use App\PagoOnline;
use \Carbon\Carbon;

class PagoFacilController extends Controller {

	public function Trans(Request $request) {
		$validator = Validator::make(
        	$request->all(), 
        	array(
            	'monto'       => 'required',
            	'email'       => 'required',
            	'prop_id'     => 'required'
           	)
        );

        if ($validator->fails()) {
        	$retorno['errors'] = true;
        	$retorno["msj"] = $validator->errors();
        } else { 
            if ($request->has("new_plan_id")) {
                $new_plan = $request->new_plan_id;
            } else {
                $new_plan = 0;
            }
			$transaccion = new Transaccion(
				($request->prop_id."".$new_plan."".rand(1000000, 9999999)), 
				config('app.PAGOFACIL_TOKEN_TIENDA'), 
				$request->monto, 
				config('app.PAGOFACIL_TOKEN_SERVICIO'), 
				$request->email
			);
			$transaccion->setCt_token_secret(
				config('app.PAGOFACIL_TOKEN_SECRET')
			);

			$respu = $transaccion->getArrayResponse();
    		$client = new Client();

			$data = [
				"ct_email" 			=> $respu['ct_email'],
    			"ct_monto" 			=> $respu['ct_monto'],
    			"ct_order_id" 		=> $respu['ct_order_id'],
    			"ct_token_service" 	=> config('app.PAGOFACIL_TOKEN_SERVICIO'),
    			"ct_token_tienda" 	=> config('app.PAGOFACIL_TOKEN_TIENDA'),
    			"ct_firma" 			=> $respu['ct_firma']
			];
			$response = $client->post(
				config('app.PAGOFACIL_URL'), [
					'query' => $data
				]
			);
			$retorno = $response->getBody()->getContents();
		}
		return $retorno;
	}

	public function CallBack(Request $request) {
        $prop_id = intval(((int) $request->ct_order_id) / 100000000);
        $aux = intval(((int) $request->ct_order_id) % 100000000);
        $new_plan_id = intval(((int) $aux) / 10000000);

        $propiedad = Propiedad::where(
            "id",
            $prop_id
        )->first();

        $pago_o = PagoOnline::where(
            "prop_id",
            $prop_id
        )->first();
        
        if (strcmp($request->ct_estado, "COMPLETADA") == 0) {
            if (!is_null($propiedad)) {
                $zona = $propiedad->zonaHoraria->first();
                $fecha_actual            = Carbon::now()->setTimezone(
                    $zona->nombre
                );
                
                $uno = new Carbon(
                    $pago_o->fecha_facturacion, 
                    $zona->nombre
                );

                $dos = new Carbon(
                    $pago_o->prox_fac, 
                    $zona->nombre
                );
                $pago_o->fecha_facturacion = $fecha_actual;

                if ($new_plan_id != 0) {
                    $pago_o->plan_id = $new_plan_id;
                }

                switch ($pago_o->plan_id) {
                    case 1: //mensual
                        $fecha_actual2 = Carbon::now()->setTimezone(
                            $zona->nombre
                        )->addMonths(1);
                        break;

                    case 2: //semestral
                        $fecha_actual2 = Carbon::now()->setTimezone(
                            $zona->nombre
                        )->addMonths(6);
                        break;

                    case 3: //anual
                        $fecha_actual2 = Carbon::now()->setTimezone(
                            $zona->nombre
                        )->addYear(1);
                        break;
                    
                    default:
                        $fecha_actual2 = Carbon::now()->setTimezone(
                            $zona->nombre
                        )->addMonths(1);
                        break;
                }
                $propiedad->estado_cuenta_id = 2;
                $propiedad->save();

                $pago_o->prox_fac = $fecha_actual2;
                $pago_o->estado   = 1;
                $pago_o->save();

                $user = $propiedad->user->first();
                $user->update(["paso" => 8]);

                $pago_f = PagoFacil::where(
                    "order_id",
                    $request->ct_order_id
                )->first();

                if (is_null($pago_f)) {
                    $pago_f = new PagoFacil();
                }
                
                $pago_f->order_id = $request->ct_order_id;
                $pago_f->monto    = $request->ct_monto;
                $pago_f->email    = $user->email;
                $pago_f->status   = $request->ct_estado;
                $pago_f->pago_id  = $pago_o->id;  
                $pago_f->save();
            }
        }

        return redirect(config('app.PANEL_PRINCIPAL'));
	}

	public function Retorno(Request $request) {
        $prop_id = intval(((int) $request->ct_order_id) / 100000000);
        $aux = intval(((int) $request->ct_order_id) % 100000000);
        $new_plan_id = intval(((int) $aux) / 10000000);

        $propiedad = Propiedad::where(
            "id",
            $prop_id
        )->first();

        $pago_o = PagoOnline::where(
            "prop_id",
            $prop_id
        )->first();
        
        if (strcmp($request->ct_estado, "COMPLETADA") == 0) {
            if (!is_null($propiedad)) {
                $zona = $propiedad->zonaHoraria->first();
                $fecha_actual            = Carbon::now()->setTimezone(
                    $zona->nombre
                );
                
                $uno = new Carbon(
                    $pago_o->fecha_facturacion, 
                    $zona->nombre
                );

                $dos = new Carbon(
                    $pago_o->prox_fac, 
                    $zona->nombre
                );
                $pago_o->fecha_facturacion = $fecha_actual;

                if ($new_plan_id != 0) {
                    $pago_o->plan_id = $new_plan_id;
                }

                switch ($pago_o->plan_id) {
                    case 1: //mensual
                        $fecha_actual2 = Carbon::now()->setTimezone(
                            $zona->nombre
                        )->addMonths(1);
                        break;

                    case 2: //semestral
                        $fecha_actual2 = Carbon::now()->setTimezone(
                            $zona->nombre
                        )->addMonths(6);
                        break;

                    case 3: //anual
                        $fecha_actual2 = Carbon::now()->setTimezone(
                            $zona->nombre
                        )->addYear(1);
                        break;
                    
                    default:
                        $fecha_actual2 = Carbon::now()->setTimezone(
                            $zona->nombre
                        )->addMonths(1);
                        break;
                }
                $propiedad->estado_cuenta_id = 2;
                $propiedad->save();

                $pago_o->prox_fac = $fecha_actual2;
                $pago_o->estado   = 1;
                $pago_o->save();

                $user = $propiedad->user->first();
                $user->update(["paso" => 8]);

                $pago_f = PagoFacil::where(
                    "order_id",
                    $request->ct_order_id
                )->first();

                if (is_null($pago_f)) {
                    $pago_f = new PagoFacil();
                }
                
                $pago_f->order_id = $request->ct_order_id;
                $pago_f->monto    = $request->ct_monto;
                $pago_f->email    = $user->email;
                $pago_f->status   = $request->ct_estado;
                $pago_f->pago_id  = $pago_o->id;  
                $pago_f->save();
            }
        }

        return redirect(config('app.PANEL_PRINCIPAL'));
	}
}