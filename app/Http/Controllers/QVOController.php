<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Response;

class QVOController extends Controller {
    
    public function ClienteCreate(Request $request) {
    	if ($request->has('correo') && $request->has('nombre')) {
    		$client = new Client();
    		try {
    			$response = $client->request(
					'POST',  
					config('app.qvo_url_base').'/customers', [
  						'json' => [
    						'email' => $request->correo,
    						'name'  => $request->nombre
  						],
  						'headers' => [
    						'Authorization' => 'Bearer '.config('app.qvo_key')
  						]
					]
				);

    			$retorno['errors'] = false;
				$retorno['msj'] = $response->json();
    		} catch (GuzzleException $e) {
    			$retorno['errors'] = true;
    			$retorno['msj'] = json_decode((string)$e->getResponse()->getBody());
    		}
    	} else {
    		$retorno['errors'] = true;
			$retorno['msj'] = "Datos requeridos";
    	}
		return Response::json($retorno);
    }

    public function ClienteRead(Request $request) {
    	if ($request->has('qvo_id')) {
    		$client = new Client();

    		try {
    			$response = $client->request(
					'GET',  
					config('app.qvo_url_base').'/customers/'.$request->qvo_id, [
  						'headers' => [
    						'Authorization' => 'Bearer '.config('app.qvo_key')
  						]
					]
				);

    			$status            = trans('request.success.code');
    			$retorno['errors'] = false;
				$retorno['msj']    = $response->json();
    		} catch (GuzzleException $e) {
    			$status            = trans('request.failure.code.not_founded');
    			$retorno['errors'] = true;
    			$retorno['msj']    = json_decode((string)$e->getResponse()->getBody());
    		}
    	} else {
    		$status            = trans('request.failure.code.bad_request');
    		$retorno['errors'] = true;
			$retorno['msj']    = "Datos requeridos";
    	}
		return Response::json($retorno, $status);
    }
}