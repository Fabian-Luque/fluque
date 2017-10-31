<?php

namespace App\Jobs;

use App\Jobs\Job;
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

    public function __construct(User $user) {
        $this->user = $user;       
    }

    public function handle() {
        echo "\n";
        $client = new Client();
        $user = User::find($this->user["id"]);

        try {
            $body = $client->request('POST', 
                config('app.qvo_url_base').'/plans', [
                    'json' => [
                        'id' => $user->propiedad[0]->id,
                        'name' => $user->propiedad[0]->nombre,
                        'price' => $user->propiedad[0]->numero_habitaciones*,// realizar calculo
                        'currency' => 'CLP',
                        'trial_period_days' => 15
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

    }

    public function failed() {
        echo "\n";
        echo "Por alguna razon fallo!!";
        echo "\n";
    }
}