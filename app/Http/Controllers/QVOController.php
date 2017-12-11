<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Jobs\CrearClienteQVO;
use App\Jobs\CrearPlanQVO;
use App\Jobs\CrearSubscripcionQVO;
use App\Jobs\CreateSubsTarjeta;
use App\Jobs\ProcesoQVO;
use App\User;
use App\Propiedad;
use App\QvoUser;
use Response;

class QVOController extends Controller {
	public function ClienteCreate(Request $request) {
		if ($request->has('correo')) {
			$user = User::where(
				"email",
				$request->correo
			)->first();
			if ($user != null) {
				\Log::info($user);
				$this->dispatch(new ProcesoQVO($user));

				$status            = trans('request.success.code');
				$retorno['errors'] = true;
				$retorno['msj']    = "Proceso QVO se ha iniciado";
			} else {
				$status            = trans('request.failure.code.bad_request');
				$retorno['errors'] = true;
				$retorno['msj']    = "Usuario no registrado";
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
			$propiedad = Propiedad::find($request->user_id);
			$client = new Client();

			if (isset($propiedad->id)) {
				$qvo_user = QvoUser::where(
					'prop_id',
					$propiedad->id
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
						$retorno["msj"]    = json_decode(
							(string)$e->getResponse()->getBody()
						);
					} 
				} else {
					$retorno["msj"] = "No existe el cliente para la subscripcion de tarjeta";
				}
			} else {
				$retorno["msj"] = "No existe el usuario para la subscripcion de tarjeta";
			}
		} else {
			$retorno["msj"] = "Datos requeridos";
		}
		return Response::json($retorno);
	}

	public function ProcesoQVO(Request $request) {
		if ($request->has('prop_id')) {
		$propiedad = Propiedad::find($request->prop_id);
		$client = new Client();

		if (!is_null($propiedad->id)) {
			try {
				$body = $client->request(
					'POST',  
					config('app.qvo_url_base').'/customers', [
						'json' => [
							'email' => $propiedad->user[0]->email,
							'name'  => $propiedad->nombre
						],
						'headers' => [
							'Authorization' => 'Bearer '.config('app.qvo_key')
						]
					]
				)->getBody();
	
				$response = json_decode($body);
				$retorno['errors'] = false;
				$retorno["msj"] = $response;

				$qvo_user = new QvoUser();
				$qvo_user->prop_id = $propiedad->id;
				$qvo_user->qvo_id  = $response->id;
				$qvo_user->save();

					try {
						$body = $client->request(
							'POST', 
							config('app.DOLAR_PRICE_API')
						)->getBody();
			
						$dolar_price = json_decode($body); 
						$dolar_price = intval($dolar_price->quotes->USDCLP);
					} catch (GuzzleException $e) {
						$dolar_price = 600;
					}

					if ($propiedad->numero_habitaciones > 27) {
						$precio = round($dolar_price * 27);    
					} else {
						$precio = round(
							$dolar_price * $propiedad->numero_habitaciones
						);
					}

					try {
						$body = $client->request(
							'POST',  
							config('app.qvo_url_base').'/plans', [
								'json' => [
									'id' => $propiedad->id,
									'name' => $propiedad->nombre,
									'price' => $precio * intval(
										config('app.PRECIO_X_HAB_QVO')
									),
									'currency' => 'CLP',
									'interval' => 'year',
									'trial_period_days' => 15
								],
								'headers' => [
									'Authorization' => 'Bearer '.config('app.qvo_key')
								]
							]
						)->getBody();

						$response = json_decode($body);

						try {
								$body = $client->request(
									'POST', 
									config('app.qvo_url_base').'/subscriptions', [
											'json' => [
												'customer_id' => $qvo_user->qvo_id,
												'plan_id' => $propiedad->id
											],
											'headers' => [
												'Authorization' => 'Bearer '.config('app.qvo_key')
											]
										]
								)->getBody();
								$response = json_decode($body);

								$qvo_user->solsub_id = $response->id;
								$qvo_user->save(); 

								$retorno['errors'] = false;
								$retorno["msj"]    = $response;
							} catch (GuzzleException $e) {
								$status            = trans('request.failure.code.bad_request');
								$retorno['errors'] = true;
								$retorno["msj"]    = json_decode(
									(string)$e->getResponse()->getBody()
								);
							} 
					} catch (GuzzleException $e) {
						$status            = trans('request.failure.code.bad_request');
						$retorno['errors'] = true;
						$retorno["msj"] = json_decode(
							(string)$e->getResponse()->getBody()
						);
					}  
			} catch (GuzzleException $e) {
				$status            = trans('request.failure.code.bad_request');
				$retorno['errors'] = true;
				$retorno["msj"] = json_decode(
					(string)$e->getResponse()->getBody()
				);
			} 
		} else {
			$status            = trans('request.failure.code.bad_request');
			$retorno['errors'] = true;
			$retorno['msj']    = "El usuario no se encuentra registrado";
		}
	} else {
		$status            = trans('request.failure.code.bad_request');
		$retorno['errors'] = true;
		$retorno['msj']    = "Datos requeridos";
	}
		return Response::json($retorno);
	}

	public function getInfoQVO(Request $request) {
		if ($request->has('prop_id')) {
			$propiedad = Propiedad::find($request->prop_id);

			if (isset($propiedad->id)) {
				$retorno['errors'] = false;
				$retorno['msj']    = $propiedad->QVO;
			} else {
				$retorno['errors'] = true;
				$retorno['msj']    = "La propiedad no se encuentra registrada en nuestra Base de datos";
			}
		} else {
			$retorno['errors'] = true;
			$retorno['msj']    = "Datos requeridos";
		}
		return Response::json($retorno);
	}


	public function TarjetaxDefectoModificar(Request $request) {
        $validator = Validator::make(
        	$request->all(), 
        	array(
            	'prop_id' 	 => 'required',
            	'tarjeta_id' => 'required',
        	)
        );

        if ($validator->fails()) {
        	$retorno['errors'] = true;
        	$retorno["msj"] = $validator->errors();
        } else {
        	$propiedad = Propiedad::find($request->prop_id);
			try {
				$qvo_user = QvoUser::where(
					'prop_id',
					$request->prop_id
				)->first();

				$client = new Client();
				$body = $client->request(
					'PUT',  
					config('app.qvo_url_base').'/customers', [
						'json' => [
							'customer_id' => $qvo_user->qvo_id,
							'email' => $propiedad->user[0]->email,
							'name'  => $propiedad->nombre,
							'default_payment_method_id' => $request->tarjeta_id
						],
						'headers' => [
							'Authorization' => 'Bearer '.config('app.qvo_key')
						]
					]
				)->getBody();
	
				$response = json_decode($body);
				$retorno['errors'] = false;
				$retorno["msj"] = $response;
			} catch (GuzzleException $e) {
				$retorno['errors'] = true;
				$retorno["msj"] = json_decode(
					(string)$e->getResponse()->getBody()
				);
			}
		}
		return Response::json($retorno); 
	}
}