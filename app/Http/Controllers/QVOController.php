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

    			$status            = trans('request.success.code');
    			$retorno['errors'] = false;
				$retorno['msj']    = $response->json();
    		} catch (GuzzleException $e) {
    			$status            = trans('request.failure.code.bad_request');
    			$retorno['errors'] = true;
    			$retorno['msj']    = json_decode((string)$e->getResponse()->getBody());
    		}
    	} else {
    		$status            = trans('request.failure.code.bad_request');
    		$retorno['errors'] = true;
			$retorno['msj']    = "Datos requeridos";
    	}
		return Response::json($retorno);
    }

    public function ClienteRead(Request $request) {
    	if ($request->has('qvo_id')) {
    		$client = new Client();
    		try {
    			$body = $client->request(
					'GET',  
					config('app.qvo_url_base').'/customers/'.$request->qvo_id, [
  						'headers' => [
    						'Authorization' => 'Bearer '.config('app.qvo_key')
  						]
					]
				)->getBody(); 

				$response = json_decode($body); 

    			$status            = trans('request.success.code');
    			$retorno['errors'] = false;
				$retorno['msj']    = $response;
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

   	public function PlanCreate(Request $request) {
   		if ($request->has('nombre') && $request->has('n_hab')) {
   			$client = new Client();
   			try {
   				$response = $client->request('POST', 
        			config('app.qvo_url_base').'/plans', [
            			'json' => [
            				'id' => $request->nombre,
        					'name' => $request->nombre,
    						'price' => 150*$request->n_hab,
    						'currency' => 'CLP',
      						'trial_period_days' => 15
    					],
        				'headers' => [
     						'Authorization' => 'Bearer '.config('app.qvo_key')
                		]
            		]
        		);

   				$status            = trans('request.success.code');
        		$retorno['errors'] = false;
				$retorno['msj'] = $response->json();
   			} catch (GuzzleException $e) {
   				$status            = trans('request.failure.code.bad_request');
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