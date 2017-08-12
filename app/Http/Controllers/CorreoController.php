<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use \Mail;
use Response;

class CorreoController extends Controller {
    
    public function sendmail(Request $request) {

        try {
            $data = array('name' => 'daniel', );
            $path = 'welcome';

            Mail::send(
                $path, 
                $data, 
                function($message) {
                    $message->to(
                        'dheresmann2012@alu.uct.cl', 
                        'daniel'
                    )->subject('Laravel First');
                    $message->from(
                        env('MAIL_USERNAME'),
                        'Our Code World'
                    );
                }
            );

            $data['errors'] = false;
            $data['msg']    = 'El link para restablecer su contraseña ha sido enviado a su correo';
        } catch (Exception $e) {
            $data['errors'] = true;
            $data['msg']    = 'Error al enviar datos';
        }

        return Response::json($data);
    }
}