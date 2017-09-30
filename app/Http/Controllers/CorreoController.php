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
use App\ResetPass; 

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
        $rules = array(
            'destino'                => 'required',
            'pdf'               => 'required',
            'namefile'               => 'required',
            'user_name'            => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            $data['errors'] = true;
            $data['msg']    = $validator->messages();
        } else {

            try {
                $path = 'auth.emails.correo';

                Mail::send(
                    $path, 
                    ['request' => $request],
                    function($message) use ($request) {
                        $message->to(
                            $request->destino, 
                            $request->destino
                        )->subject('Mensaje de GoFeels');
                        $message->attach(
                            $request->pdf,
                            ['as' => $request->namefile]
                        );
                    }
                );
                
                $data['errors'] = false;
                $data['msg']    = 'Correo enviado de forma exitoso';
            } catch (Exception $e) {
                $data['errors'] = true;
                $data['msg']    = 'Error al enviar datos';
            }
        }
        return Response::json($data); 
    }
}