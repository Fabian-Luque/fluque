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
            $transaccion = new Transaccion(
                (
                    $request->prop_id."".
                    $request->plan_id."".
                    rand(100000, 999999)."".
                    $request->interval_time
                ), 
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
                "ct_email"          => $respu['ct_email'],
                "ct_monto"          => $respu['ct_monto'],
                "ct_order_id"       => $respu['ct_order_id'],
                "ct_token_service"  => config('app.PAGOFACIL_TOKEN_SERVICIO'),
                "ct_token_tienda"   => config('app.PAGOFACIL_TOKEN_TIENDA'),
                "ct_firma"          => $respu['ct_firma']
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
        $interval_time = (int) substr($request->ct_order_id, -1);
        $data = substr($request->ct_order_id, 0, -1);
        $prop_id = intval(((int) $data) / 10000000);
        $aux = ("".(((int) $data) % 10000000) / 100000);
        $plan_id = (int) substr($aux, 0, 1);
    
        Event::fire(
            new PagoFacilEvent(
                "pagofacil",
                $request->all(),
                $prop_id
            )
        );
        
        if (strcmp($request->ct_estado, "COMPLETADA") == 0) {
            if (!is_null($propiedad)) {
                $request->merge([ 
                    'prop_id'       => $prop_id
                ]);
                $request->merge([ 
                    'pas_pago_id'   => 1
                ]);
                $request->merge([ 
                    'estado'        => 1
                ]);
                $request->merge([ 
                    'plan_id'       => $plan_id
                ]);
                $request->merge([ 
                    'interval_time' => $interval_time
                ]);

                $resp = app('App\Http\Controllers\RegistroController')->SeleccionPago(
                    $request
                );

                $propiedad = Propiedad::where(
                    "id",
                    $prop_id
                )->first();

                $pago_o = PagoOnline::where(
                    "prop_id",
                    $prop_id
                )->first();

                $propiedad->estado_cuenta_id = 2;
                $propiedad->save();

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

                $pago_o->estado   = 1;
                $pago_o->save();
            } 
        } 
        return redirect(config('app.PANEL_PRINCIPAL'));
    }

    public function Retorno(Request $request) {
        $interval_time = (int) substr($request->ct_order_id, -1);
        $data = substr($request->ct_order_id, 0, -1);
        $prop_id = intval(((int) $data) / 10000000);
        $aux = ("".(((int) $data) % 10000000) / 100000);
        $plan_id = (int) substr($aux, 0, 1);
    
        Event::fire(
            new PagoFacilEvent(
                "pagofacil",
                $request->all(),
                $prop_id
            )
        );
        
        if (strcmp($request->ct_estado, "COMPLETADA") == 0) {
            if (!is_null($propiedad)) {
                $request->merge([ 
                    'prop_id'       => $prop_id
                ]);
                $request->merge([ 
                    'pas_pago_id'   => 1
                ]);
                $request->merge([ 
                    'estado'        => 1
                ]);
                $request->merge([ 
                    'plan_id'       => $plan_id
                ]);
                $request->merge([ 
                    'interval_time' => $interval_time
                ]);

                $resp = app('App\Http\Controllers\RegistroController')->SeleccionPago(
                    $request
                );

                $propiedad = Propiedad::where(
                    "id",
                    $prop_id
                )->first();

                $pago_o = PagoOnline::where(
                    "prop_id",
                    $prop_id
                )->first();

                $propiedad->estado_cuenta_id = 2;
                $propiedad->save();

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

                $pago_o->estado   = 1;
                $pago_o->save();
            } 
        } 
        return redirect(config('app.PANEL_PRINCIPAL'));
    }
}