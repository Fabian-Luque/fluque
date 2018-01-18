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


class SendMail extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels;

    protected $array;

    /**
     * Create a new job instance.
     *
     * @return void
     */ 
    public function __construct(Propiedad $propiedad, $cliente_email, $propiedad_email, $vista_coreo, $vista_pdf, $nombre_pdf, $arr) {
        echo "\nempezo";

        $this->array = array(
            'propiedad'       => $propiedad,
            'cliente_email'   => $cliente_email,
            'propiedad_email' => $propiedad_email,
            'vista_coreo'     => $vista_coreo,
            'vista_pdf'       => $vista_pdf,
            'nombre_pdf'      => $nombre_pdf,
            'arr'             => $arr
        ); 
        echo "\nse construyo\n";
    }

    /**
     * Execute the job.
     *
     * @param  Mailer  $mailer
     * @return void
     */
    public function handle(Mailer $mailer) {
        echo "funcaaa";
        $array = $this->array;

        try {
            $mailer->send(
                $this->array['vista_coreo'], 
                ['array' => $array],
                function($message) use ($array) {
                    $message->to(
                        $array['cliente_email'], 
                        $array['cliente_email']
                    )->subject('Mensaje de '.$array['propiedad']->nombre);
                    
                    if ($array['propiedad_email'] != false) {
                        $message->cc($array['propiedad_email']);
                    }

                    if (strlen($array['vista_pdf']) != 0) {
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