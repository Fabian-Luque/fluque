<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Jobs\CrearPlanQVO;
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

class CrearClienteQVO extends Job implements SelfHandling, ShouldQueue {
    use InteractsWithQueue, SerializesModels;
    public $user;

    public function __construct(User $user) {
        $this->user = $user;       
    }

    public function handle() {
    	echo "\n";
    	$client = new Client();

        try {
            $body = $client->request(
                'POST',  
                config('app.qvo_url_base').'/customers', [
                    'json' => [
                        'email' => $this->user->propiedad[0]->email,
                        'name'  => $this->user->propiedad[0]->nombre
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
            $qvo_user->save(); 

            echo "\nCliente creado con exito\n";
            $job = (new CrearPlanQVO($user))->delay(5);
            dispatch($job);
            
        } catch (GuzzleException $e) {
            echo $retorno["msj"];
            $retorno["msj"]    = json_decode((string)$e->getResponse()->getBody());
            $job = (new CrearPlanQVO($user))->delay(5);
            dispatch($job);
        }
    }

    public function failed() {
        echo "\n";
        echo "Por alguna razon fallo!!";
        echo "\n";
    }
}