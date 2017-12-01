<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\User;
use App\QvoUser;
use Response;

class ProcesoQVO extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels;
    public $user;

    public function __construct(User $user) {
        $this->user = $user;    
    }

    public function handle() {
        $user = User::find($this->user->id);
        $client = new Client();

        \Log::info($user);
        if (!is_null($user)) {
            try {
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


                        if ($qvo_user != null) {
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

        \Log::info('Proceso Funcionando - ProcesoQVO');
    }

    public function failed() {
        \Log::error("Por alguna razon fallo - ProcesoQVO");
    }
}
