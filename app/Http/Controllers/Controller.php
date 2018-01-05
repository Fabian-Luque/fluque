<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use App\Propiedad;
use PDF;
use App\Jobs\SendMail;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    public function EnvioCorreo(Propiedad $propiedad, $cliente_email, $arr, $vista_coreo, $vista_pdf, $nombre_pdf, $opcion, $propiedad_email) {

        if ($opcion == 0) { // solo descarga
            $pdf = PDF::loadView(
                $vista_pdf, 
                $arr
            );

            return $pdf;
        } elseif ($opcion == 1) { // solo envio de correo
            $job = new SendMail(
                $propiedad,
                $cliente_email,
                $propiedad_email,
                $vista_coreo,
                $vista_pdf,
                $nombre_pdf,
                $arr
            );

            $this->dispatch($job);
            return;
        } else { // ambos!!!!!!!!!!!!!!!!!!!!
            $job = new SendMail(
                $propiedad,
                $cliente_email,
                $propiedad_email,
                $vista_coreo,
                $vista_pdf,
                $nombre_pdf,
                $arr
            );

            $this->dispatch($job);

            $pdf = PDF::loadView(
                $vista_pdf, 
                $arr
            );

            return $pdf;
        } 
    }
}
