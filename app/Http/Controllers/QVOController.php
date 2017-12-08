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
use App\Jobs\ProcesoQVO;
use App\User;
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
			$user = User::find($request->user_id);
			$client = new Client();

			if (isset($user->id)) {
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


	public function ejmqvo(Request $request) {
		$user = User::find($request->id);
        $client = new Client();

        \Log::info($user);
        if (!is_null($user)) {
            \Log::info("si es nulo\n");
            try {
                \Log::info("peticion http\n");
                $body = $client->request(
                    'POST',  
                    config('app.qvo_url_base').'/customers', [
                        'json' => [
                            'email' => $user->email,
                            'name'  => $user->propiedad[0]->nombre
                        ],
                        'headers' => [
                            'Authorization' => 'Bearer '.config('app.qvo_key')
                        ]
                    ]
                )->getBody();
    

                $response = json_decode($body);
                $retorno["msj"] = $response;

                $qvo_user = new QvoUser();
                $qvo_user->prop_id = $user->propiedad[0]->id;
                $qvo_user->qvo_id  = $response->id;
                
                if ($qvo_user->save()) {
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

                    if ($user->propiedad[0]->numero_habitaciones > 27) {
                        $precio = round($dolar_price * 27);    
                    } else {
                        $precio = round(
                            $dolar_price * $user->propiedad[0]->numero_habitaciones
                        );
                    }

                    try {
                        $body = $client->request(
                            'POST',  
                            config('app.qvo_url_base').'/plans', [
                                'json' => [
                                    'id' => $user->propiedad[0]->id,
                                    'name' => $user->propiedad[0]->nombre,
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


                        if (isset($qvo_user->id)) {
                            $job = (new CrearSubscripcionQVO($user))->delay(5);
                            dispatch($job);
                        } 
                    } catch (GuzzleException $e) {
                        $retorno["msj"] = json_decode(
                            (string)$e->getResponse()->getBody()
                        );
                    } 
                } else {
                    \Log::error('Error al crear el cliente');
                }
            } catch (GuzzleException $e) {
                $retorno["msj"] = json_decode(
                    (string)$e->getResponse()->getBody()
                );
            } 
        } else {
            $status            = trans('request.failure.code.bad_request');
            $retorno['errors'] = true;
            $retorno['msj']    = "El usuario no se encuentra registrado";
        }

        return Response::json($retorno);
	}
}