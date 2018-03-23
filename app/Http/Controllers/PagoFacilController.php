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

class PagoFacilController extends Controller {

	public function Prueba(Request $request) {
		$validator = Validator::make(
        	$request->all(), 
        	array(
            	'email'   => 'required',
            	'prop_id' => 'required',
           	)
        );

        if ($validator->fails()) {
        	$retorno['errors'] = true;
        	$retorno["msj"] = $validator->errors();
        } else {
			$order_id_tienda = "123456";
			$token_tienda = "1214124";
			$amount = "100.00";
			$email = "facturacion@gofeels.com";

			$transaccion = new Transaccion(
				$order_id_tienda, 
				$token_tienda, 
				$amount, 
				config('app.PAGOFACIL_TOKEN_SERVICIO'), 
				$email
			);
			$transaccion->setCt_token_secret(
				config('app.PAGOFACIL_TOKEN_SECRET')
			);
			$pago_args = $transaccion->getArrayResponse();
		}
		return Response::json($pago_args);
	}
}