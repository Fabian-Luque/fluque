<?php

namespace App\Jobs;

use App\Propiedad;
use App\Jobs\Job;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use \Mail;

class SendMail extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels;

    protected $array;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Propiedad $propiedad, $destino, $vista, $pdf=null, $nombre_pdf=null) {
        echo "empezo";
        $this->array = array(
            'destino' => $destino,
            'vista' => $vista,
            'propiedad' => $propiedad,
            'pdf' => $pdf,
            'nombre_pdf' => $nombre_pdf,
        ); 
        echo "se construyo";
    }

    /**
     * Execute the job.
     *
     * @param  Mailer  $mailer
     * @return void
     */
    public function handle(Mailer $mailer) {
        echo "funcaaa";
        echo $this->array['vista'];
        
        echo "\n";
        echo $this->array['pdf'];

        $array = $this->array;
        try {
            Mail::send(
                $this->array['vista'], 
                ['array' => $array],
                function($message) use ($array) {
                    $message->to(
                        $array['destino'], 
                        $array['destino']
                    )->subject('Mensaje de '.$array['propiedad']->nombre);
                    
                    if ($array['pdf'] != null) {
                        $message->attachData(
                            $array['pdf'], 
                            $array['nombre_pdf']
                        );
                    }
                }
            );
        } catch(\Exception $e){
            echo "error ".$e->getMessage();
        }
        echo "\nexito";
    }

    public function failed() {
        echo "\n";
        echo "Por alguna razon fallo!!";
        echo "\n";
    }
}