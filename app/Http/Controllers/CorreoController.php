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
use Illuminate\Support\Facades\Validator;
use App\User;  
use App\Propiedad; 
use App\ResetPass; 
use PDF;

class CorreoController extends Controller {
    
    public function sendmail(Request $request, $token = null) {
        if ($request->has('destino')) {
            $user = User::where(
                'email', 
                $request->destino
            )->first();

            if (!is_null($user)) {

                try {
                    $path = 'auth.emails.password';
                    $reset_pass = ResetPass::where(
                        'email', 
                        $request->destino
                    )->first();

                    if (!is_null($reset_pass)) {
                        $reset_pass->delete();

                        $reset_pass_new = new ResetPass();
                        $reset_pass_new->email = $request->destino;
                        $reset_pass_new->token = str_random(64);
                        $reset_pass_new->save();

                        $request->token = $reset_pass_new->token;
                    } else {
                        $reset_pass_new = new ResetPass();
                        $reset_pass_new->email = $request->destino;
                        $reset_pass_new->token = str_random(64);
                        $reset_pass_new->save();

                        $request->token  = $reset_pass_new->token;
                    }

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


    public function SendFileByEmail(Request $request) {
        try {
            $ped = PDF::loadView(
                "auth.emails.correo",
                []
            );

            $propiedad = Propiedad::find(4);
            

            $array = array(
                'pdf' => $ped->stream(), 
                'namefile' => "miarch.pdf",
                'destino' => $request->destino,
                'vista' => "correos.comprobante_reserva",
                'propiedad' => $propiedad,
            ); 

            Mail::send(
                $array['vista'], 
                ['array' => $array],
                function($message) use ($array) {
                    $message->to(
                        $array['destino'], 
                        $array['destino']
                    )->subject('Restablecer contraseña GoFeels');
                    
                    /*
                    $message->attachData(
                        $array['pdf'], 
                        'comprobante.pdf'
                    );
                    */
                }
            );


            $data['errors'] = false;
            $data['msg']    = 'Correo enviado de forma exitoso';
        } catch (Exception $e) {
            $data['errors'] = true;
            $data['msg']    = 'Error al enviar datos';
        }
        return Response::json($data); 
    }
}