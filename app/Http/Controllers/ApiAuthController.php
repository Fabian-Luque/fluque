<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\User;
use App\ResetPass;
use App\Estadocuenta;
use Illuminate\Http\Response as HttpResponse;
use Response;
use DB;
use \Carbon\Carbon;

class ApiAuthController extends Controller {
	public function signin(Request $request) {
        $credentials = $request->only('email', 'password');
        $user = User::where('email', $credentials['email'])->with('propiedad')->first();

        if(!is_null($user)) {
            $user_id = $user->id;
            $propiedad_id = $user->propiedad[0]['id'];

            if (strcmp($request->email, config('app.MAIL_USERNAME')) != 0) {
                switch ($user->propiedad[0]->estado_cuenta_id) {
        		    case '3': //
                        $data['errors'] = trans('request.failure.status');
                        $data['msg']    = 'Su cuenta de prueba a caducado';
                        $status         = trans('request.failure.code.forbidden');
        		    break;

    			    default:
                        if ($user->estado_id == 1) {
                            if (!$token = JWTAuth::attempt($credentials)) {
                                $data['errors'] = trans('request.failure.status');
                                $data['msg']    = 'Usuario o contraseña incorrecta';
                                $status         = trans('request.failure.code.forbidden');
                            } else {
                                $data   = compact('token', 'user_id', 'propiedad_id');
                                $status = trans('request.success.code');
                            }
                        } else {   
                            $data['errors'] = trans('request.failure.status');
                            $data['msg']    = 'Su cuenta se encuentra inactiva';
                            $status         = trans('request.failure.code.forbidden');
                        }
        		    break;
        	    }
            } else {
                $data['errors'] = trans('request.failure.status');
                $data['msg']    = 'Su cuenta se encuentra inactiva';
                $status         = trans('request.failure.code.forbidden');
            }
        } else {
        	$data['errors'] = trans('request.failure.status');
        	$data['msg']  	= trans('request.failure.bad');
            $status         = trans('request.failure.code.not_founded');
        } 
        return Response::json($data, $status); 
    }

    public function  ResetPassUser(Request $request) {
        if ($request->has('email') && $request->has('password') && $request->has('actual')) {
            $user =  User::where('email', $request->email)->first();
            if (!is_null($user)) {
               if ($user->VerifyPassword($request->password) == 0) {
                    if ($user->VerifyPassword($request->actual) == 1) {
                        $user->setPasswordAttribute($request->password);
                        $user->save();

                        $data['errors'] = trans('request.success.status');
                        $data['msg']    = 'Su contraseña ha sido actualizada'; 
                        $status         = trans('request.success.code');
                    } else {
                        $data['errors'] = trans('request.failure.status');
                        $data['msg']    = 'Contraseña actual ingresada no valida';
                        $status         = trans('request.failure.code.forbidden');
                    }
               } else {
                    $data['errors'] = trans('request.failure.status');
                    $data['msg']    = 'Utilice una contraeña distinta a la actual';
                    $status         = trans('request.failure.code.forbidden');
               }
            } else {
                $data['errors'] = trans('request.failure.status');
                $data['msg']    = 'Usuario no exite';
                $status         = trans('request.failure.code.not_founded');
            }
        } else {
            $data['errors'] = trans('request.failure.status');
            $data['msg']    = 'Datos requeridos';
            $status         = trans('request.failure.code.bad_request');
        }
        return Response::json($data);
    }

    public function ResetPassword(Request $request, $token=null) {
        $carbon = new Carbon();
        $date = $carbon->now();

        if ($request->has('email') && $request->has('password') && $request->has('passwordc') && $request->has('token_reset')) {

            $user =  User::where('email', $request->email)->first();
            $rpass = ResetPass::where('email', $user->email)->first();
            if (strcmp($request->password, $request->passwordc) == 0) {
            if (!is_null($rpass)) {
                if (!is_null($user)) {

                    $now = Carbon::now()->setTimezone('America/Santiago');
                    
                    $tstamp_token = Carbon::createFromFormat(
                        'Y-m-d H:i:s', 
                        $rpass->created_at 
                    ); 

                    $time_token = round(
                        abs(
                            strtotime(
                                $now->toDateTimeString()
                            ) - strtotime(
                                $tstamp_token->toDateTimeString()
                            )
                        ) / 60,0
                    );

                    if ((strcmp($request->token_reset, $rpass->token) == 0) && intval($time_token) < 10) {
                        if ($user->VerifyPassword($request->password) == 0) {
                            $user->setPasswordAttribute($request->password);
                            $user->save();
                            $rpass->delete();

                            $data['errors'] = false;
                            $data['msg']    = 'Su contraseña ha sido actualizada'; 
                        } else {
                            $data['errors'] = true;
                            $data['msg']    = 'Por seguridad utilice una contraseña distinta'; 
                            $data['tok']    = $request->token;

                            return redirect(
                                'resetpass'
                            )->with('respuesta', $data);
                        }
                    } else {
                        $data['errors'] = true;
                        $data['msg']    = 'La peticion de cambio de contraseña a caducado';
                    }
                } else {
                    $data['errors'] = true;
                    $data['msg']    = 'Acceso denegado';
                }
            } else {
                $data['errors'] = true;
                $data['msg']    = 'La peticion de cambio de contraseña a caducado';
            }
        } else {
            $data['errors'] = true;
            $data['msg']    = 'Confirmacion de contraseña no coincide';

            return redirect(
                'resetpass'
            )->with('respuest', $data);  
        }

            return redirect(
                'sendmailreset'
            )->with('respuesta', $data);   
       
             
        } elseif (!is_null($token)) {
            return redirect(
                'resetpass'
            )->with('token_reset', $token);
        } else {
            $data['errors'] = true;
            $data['msg']    = 'Sesion expirada';
            
            return redirect(
                'sendmailreset'
            )->with('respuesta', $data);
        }
    }
}