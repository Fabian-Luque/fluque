<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Jobs\CrearClienteQVO;
use App\Jobs\CrearPlanQVO;
use App\Jobs\CrearSubscripcionQVO;
use App\Jobs\CreateSubsTarjeta;
use App\User;
use App\QvoUser;
use Response;

class QVOController extends Controller {
	public function ClienteCreate(Request $request) {
		if ($request->has('correo') && $request->has('nombre')) {
			$client = new Client();
			$user = User::where(
				'email',
				$request->correo
			)->first();

			$qvo_user = QvoUser::where(
            	'prop_id',
            	$user->propiedad[0]->id
        	)->first();

        	if (!$qvo_user->solsub_id) {
        		$job = new CrearClienteQVO($user);
				dispatch($job);
				$retorno['errors'] = false;
				$retorno["msj"] = "El cliente ha sido registrado en QVO";
        	} else {
        		$retorno['errors'] = true;
				$retorno["msj"] = "El ya se encuentra registrado en QVO";
        	}
		} else {
			$status            = trans('request.failure.code.bad_request');
			$retorno['errors'] = true;
			$retorno['msj']    = "Datos requeridos";
		}
		return Response::json($retorno);
	}

	public function SubsTarjeta(Request $request) {
		if ($request->has('user_id') && $request->has('url_retorno')) {
			$user = User::find($request->user_id);
			$client = new Client();
			$qvo_user = QvoUser::where(
				'prop_id',
				$user->propiedad[0]->id
			)->first();

			if (isset($qvo_user->prop_id)) {
				try {
					$body = $client->request(
						'POST', 
						config('app.qvo_url_base').'/customers/'.$qvo_user->qvo_id.'/cards/inscriptions', [
							'json' => [
								'return_url' => $request->url_retorno,
							],
							'headers' => [
								'Authorization' => 'Bearer '.config('app.qvo_key')
							]
						]
					)->getBody();
					
					$response = json_decode($body);
					$retorno["msj"]    = $response;
				} catch (GuzzleException $e) {
					$retorno["msj"]    = json_decode((string)$e->getResponse()->getBody());
				} 
			} else {
				$retorno["msj"] = "No existe el cliente para la subscripcion de tarjeta";
			}
		}
		return Response::json($retorno, $status);
	}
}