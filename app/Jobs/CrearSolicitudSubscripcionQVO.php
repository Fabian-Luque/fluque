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

class CrearSolicitudSubscripcionQVO extends Job implements SelfHandling, ShouldQueue {
    use InteractsWithQueue, SerializesModels;
    public $user;

    public function __construct(User $user) {
        $this->user = $user;       
    }

    public function handle() {
        echo "\n";
        $client = new Client();
        
        $user = User::find($this->user["id"]);
        $qvo_user = QvoUser::where(
            'prop_id',
            $user->propiedad[0]->id
        )->first();

        try {
            $body = $client->request(
                'POST', 
                config('app.qvo_url_base').'/subscription_requests', [
                    'json' => [
                        'customer_id' => $qvo_user->qvo_id,
                        'plan_id' => $user->propiedad[0]->id
                    ],
                    'headers' => [
                        'Authorization' => 'Bearer '.config('app.qvo_key')
                    ]
                ]
            )->getBody();
            $response = json_decode($body);

            $retorno["msj"]    = $response;

            $qvo_user->solsub_id = $response->id;
            $qvo_user->save();
        } catch (GuzzleException $e) {
            $retorno["msj"]    = json_decode((string)$e->getResponse()->getBody());
        }
        echo $retorno["msj"];
    }

    public function failed() {
        echo "\n";
        echo "Por alguna razon fallo!!";
        echo "\n";
    }
}