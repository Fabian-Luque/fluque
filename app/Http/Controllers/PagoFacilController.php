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

class PagoFacilController extends Controller {

	public function Prueba(Request $request) {
		$validator = Validator::make(
        	$request->all(), 
        	array(
            	'monto'   => 'required',
            	'email'   => 'required',
            	'prop_id' => 'required',
           	)
        );

        if ($validator->fails()) {
        	$retorno['errors'] = true;
        	$retorno["msj"] = $validator->errors();
        } else {
			$token_tienda = "1214124";

			$transaccion = new Transaccion(
				(string) Uuid::generate(4), 
				$token_tienda, 
				$request->monto, 
				config('app.PAGOFACIL_TOKEN_SERVICIO'), 
				$request->email
			);
			$transaccion->setCt_token_secret(
				config('app.PAGOFACIL_TOKEN_SECRET')
			);

			$retorno['errors'] = false;
        	$retorno["msj"] = $transaccion->getArrayResponse();
		}
		return Response::json($retorno);
	}

	public function CallBack(Request $request) {
		$validator = Validator::make(
        	$request->all(), 
        	array(
            	"ct_order_id"   		  => 'required',
		        "ct_token_tienda"   	  => 'required',
		        "ct_monto"   			  => 'required',
		        "ct_token_service"   	  => 'required',
		        "ct_estado"   			  => 'required',
		        "ct_authorization_code"   => 'required',
		        "ct_payment_type_code"    => 'required',
		        "ct_card_number"   		  => 'required',
		        "ct_card_expiration_date" => 'required',
		        "ct_shares_number"   	  => 'required',
		        "ct_accounting_date"      => 'required',
		        "ct_transaction_date"     => 'required',
		        "ct_order_id_mall"   	  => 'required',
		        "ct_firma"   			  => 'required'
           	)
        );

        if ($validator->fails()) {
        	$retorno['errors'] = true;
        	$retorno["msj"] = $validator->errors();
        } else {
        	Event::fire(
                new PagoFacilEvent(
                    $mensaje->receptor_id,
                    $conv_no_leidas->count()
                )
            );

        	$retorno['errors'] = false;
        	$retorno["msj"] = $request->all();
        }
        return Response::json($retorno);
	}

	public function Retorno(Request $request) {
		# code...
	}
}