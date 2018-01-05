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
    public function __construct($propiedad, $cliente_email, $propiedad_email, $vista_coreo, $vista_pdf, $nombre_pdf, $arr) {
        echo "empezo";

        $this->array = array(
            'propiedad'       => $propiedad,
            'cliente_email'   => $cliente_email,
            'propiedad_email' => $propiedad_email,
            'vista_coreo'     => $vista_coreo,
            'vista_pdf'       => $vista_pdf,
            'nombre_pdf'      => $nombre_pdf,
            'arr'             => $arr
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
                        $array['cliente_email'], 
                        $array['cliente_email']
                    )->subject('Mensaje de '.$array['propiedad']->nombre);
                    
                    if ($array['propiedad_email'] != false) {
                        $message->cc($array['propiedad_email']);
                    }

                    if (empty($array['arr']) != 1) {
                        $pdf = PDF::loadView(
                            $array['vista_pdf'], 
                            $array['arr']
                        );

                        $message->attachData(
                            $pdf->stream(), 
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