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
use App\Reserva;

class SendMail extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels;

    protected $array;

    /**
     * Create a new job instance.
     *
     * @return void
     */ 
    public function __construct(Propiedad $propiedad, $cliente_email, $propiedad_email, $vista_coreo, $vista_pdf, $nombre_pdf, $arr, $opp) {

        $this->array = array(
            'propiedad'       => $propiedad,
            'cliente_email'   => $cliente_email,
            'propiedad_email' => $propiedad_email,
            'vista_coreo'     => $vista_coreo,
            'vista_pdf'       => $vista_pdf,
            'nombre_pdf'      => $nombre_pdf,
            'arr'             => $arr,
            'opp'             => $opp
        ); 
    }

    /**
     * Execute the job.
     *
     * @param  Mailer  $mailer
     * @return void
     */
    public function handle(Mailer $mailer) {
        $array = $this->array;

        try {
            if (strcmp($this->array['opp'], "reservas-varias") == 0) {
                $propiedad_id = $array['propiedad']->id;

                $reservas = Reserva::whereHas(
                    'habitacion', 
                    function($query) use ($propiedad_id) {
                        $query->where('propiedad_id', $propiedad_id);
                    }
                )->orderby('id','DESC')
                ->where('numero_reserva', '!=', null)
                ->where('n_reserva_motor', $array['arr']['reserva']->n_reserva_motor);

                $iva = ($array['arr']['reserva']->monto_total * 19) / 100;
                echo "iva".$iva;
                $subtotal = $array['arr']['reserva']->monto_total - $iva;
                echo "subtotal".$subtotal;
                $porpagar = $array['arr']['reserva']->monto_total - $array['arr']['reserva']->monto_por_pagar;
                echo "porpagar".$porpagar;
                $data_correo = [
                    'reservaspdf'  => $reservas,
                    'array'        => $array,
                    'iva'          => $iva,
                    'subtotal'     => $subtotal,
                    'porpagar'     => $porpagar
                ];
            } else {
                $data_correo = [
                    'array' => $array
                ];
            }

            $mailer->send(
                $this->array['vista_coreo'], 
                $data_correo,
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
    }

    public function failed() {
        echo "\n";
        echo "Por alguna razon fallo!!";
        echo "\n";
    }
}