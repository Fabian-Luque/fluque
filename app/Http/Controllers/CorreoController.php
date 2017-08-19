<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use \Mail;
use Response;
use Password;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Foundation\Auth\ResetsPasswords;
use App\User;
//use SendsPasswordResetEmails;

class CorreoController extends Controller {
    
    public function sendmail(Request $request, $token) {
        dd($token);
        if ($request->has('destino')) {
            $user = User::where(
                'email', 
                $request->destino
            )->first();

            if (!is_null($user)) {

                try {
                    $path = 'auth.emails.password';
                    $request->token = str_random(64);
                    Mail::send(
                        $path, 
                        ['request' => $request],
                        function($message) use ($request) {
                            $message->to(
                                $request->destino, 
                                $request->destino
                            )->subject('Restablecer contraseña GoFeels');
                        }
                    );

                    $data['errors'] = false;
                    $data['msg']    = 'El link para restablecer su contraseña ha sido enviado a su correo';
            
                    return redirect(
                        'sendmailreset'
                    )->with('respuesta', $data);
     
                } catch (Exception $e) {
                    $data['errors'] = true;
                    $data['msg']    = 'Error al enviar datos';
                }
            } else {
                $data['errors'] = true;
                $data['msg']    = 'El correo ingresado no corresponde a ninguna cuenta registrada';
            
                return redirect(
                    'sendmailreset'
                )->with('respuesta', $data);
            }
        } else {
            $data['errors'] = true;
            $data['msg']    = 'Ingrese el correo asociado a la cuenta que desea restablecer';
            
            return redirect(
                'sendmailreset'
            )->with('respuesta', $data);
        }
    }
}