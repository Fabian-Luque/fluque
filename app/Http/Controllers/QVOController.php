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
					'https://playground.qvo.cl/customers', [
  						'json' => [
    						'email' => $request->correo,
    						'name'  => $request->nombre
  						],
  						'headers' => [
    						'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJjb21tZXJjZV9pZCI6ImNvbV9NZXdiQ2JoNkwwRUk2WXA3d2VDTFBnIiwiYXBpX3Rva2VuIjp0cnVlfQ.E4o_dCiIwwqg1ccaCz_SweajtYaNMjK9Gs9haXpE18E'
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
}






