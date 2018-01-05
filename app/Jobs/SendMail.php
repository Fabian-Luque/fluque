<?php

namespace App\Jobs;

use App\Propiedad;
use App\Jobs\Job;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use \Mail;
use PDF;
use Barryvdh\DomPDF\Facade as PDFF;

class SendMail extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels;

    protected $array;

    /**
     * Create a new job instance.
     *
     * @return void
     */ 
    public function __construct(Propiedad $propiedad, $destino, $destino_copia, $vista, $nombre_pdf="",$pdf) {
        echo "empezo";

        $this->array = array(
            'destino'       => $destino,
            'destino_copia' => $destino_copia,
            'vista'         => $vista,
            'propiedad'     => $propiedad,
            'nombre_pdf'    => $nombre_pdf,
            'pdf'           => $pdf,
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

        $array = $this->array;

        try {
            $mailer->send(
                $this->array['vista'], 
                ['array' => $array],
                function($message) use ($array) {
                    $message->to(
                        $array['destino'], 
                        $array['destino']
                    )->subject('Mensaje de '.$array['propiedad']->nombre);
                    
                    if ($array['destino_copia'] != false) {
                        $message->cc($array['destino_copia']);
                    }

                    if ($array['pdf'] != null) {
                        $message->attachData(
                            $array['pdf']->stream(), 
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