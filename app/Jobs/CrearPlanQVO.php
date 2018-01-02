<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Jobs\CrearSubscripcionQVO;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use App\QvoUser;

class CrearPlanQVO extends Job implements SelfHandling, ShouldQueue {
    use InteractsWithQueue, SerializesModels;
    public $user;

    public function __construct($user) {
        $this->user = $user;    
    }

    public function handle() {
        echo "\n";
        $client = new Client();

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

        if ($this->user->propiedad[0]->numero_habitaciones > 27) {
            $precio = round($dolar_price * 27);    
        } else {
            $precio = round($dolar_price * $this->user->propiedad[0]->numero_habitaciones);
        }

        try {
            $body = $client->request(
                'POST', 
                config('app.qvo_url_base').'/plans', [
                    'json' => [
                        'id' => $this->user->propiedad[0]->id,
                        'name' => $this->user->propiedad[0]->nombre,
                        'price' => $precio * intval(config('app.PRECIO_X_HAB_QVO')),
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

            $qvo_user = QvoUser::where(
                'prop_id',
                $this->user->propiedad[0]->id
            )->first();

            if ($qvo_user != null) {
                $job = (new CrearSubscripcionQVO($this->user))->delay(5);
                dispatch($job);
            } 

            $retorno["msj"]    = $response;
    
        } catch (GuzzleException $e) {
            $retorno["msj"]    = json_decode((string)$e->getResponse()->getBody());
        }   
    }

    public function failed() {
        echo "\n";
        echo "Por alguna razon fallo!!";
        echo "\n";
    }
}