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

class CrearSubscripcionQVO extends Job implements SelfHandling, ShouldQueue {
    use InteractsWithQueue, SerializesModels;
    public $user;

    public function __construct(User $user) {
        $this->user = $user;       
    }

    public function handle() {
        echo "\n";
        $client = new Client();
        $qvo_user = QvoUser::where(
            'prop_id',
            $this->user->propiedad[0]->id
        )->first();

        if (isset($qvo_user->prop_id) && isset($qvo_user->solsub_id) == true) {
            try {
                $body = $client->request(
                    'POST', 
                    config('app.qvo_url_base').'/subscriptions', [
                        'json' => [
                            'customer_id' => $qvo_user->qvo_id,
                            'plan_id' => $this->user->propiedad[0]->id
                        ],
                        'headers' => [
                            'Authorization' => 'Bearer '.config('app.qvo_key')
                        ]
                    ]
                )->getBody();
                $response = json_decode($body);

                $qvo_user->solsub_id = $response->id;
                $qvo_user->save(); 

                echo "\n";
                echo $qvo_user;
                echo "\n";

                $retorno["msj"]    = $response;
            } catch (GuzzleException $e) {
                $retorno["msj"]    = json_decode((string)$e->getResponse()->getBody());
            } 
        } else {
            echo "No existe el cliente para la subscripcion";
        }
        echo $retorno;
    }

    public function failed() {
        echo "\n";
        echo "Por alguna razon fallo!!";
        echo "\n";
    }
}