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
        $user = User::where('email', $credentials['email'])->first();
        $user_id = $user->id;

        if(!is_null($user)) {
        	switch ($user->propiedad->EstadoCuenta->estado) {
        		case '2': //
                    $data['errors'] = true;
                    $data['msg']    = 'Su cuenta de prueba a caducado';
        		break;

    			default:
                    if (!$token = JWTAuth::attempt($credentials)) {
                        $data['errors'] = true;
                        $data['msg']    = 'Usuario o contraseña incorrecta';
                        $status = HttpResponse::HTTP_FORBIDDEN;
                    } else {
                        $data = compact('token', 'user_id');
                    }
        		break;
        	}
        } else {
        	$data['errors'] = true;
        	$data['msg']  	= 'Usuario o contraseña incorrecta';
            $status = HttpResponse::HTTP_FORBIDDEN;
        } 
        return Response::json($data); 
    }

	public function userAuth(Request $request) {
		$credentials = $request->only('email', 'password');
		$token = null;

		try {
			if(!$token = JWTAuth::attempt($credentials)) {
				return response()->json(
                    ['error' => 'invalid_credentials'], 
                    401
                );
			}
		} catch(JWTException $ex) {
			return response()->json(
                ['error' => 'algo anda mal'], 
                500
            );
		}
		$user = JWTAuth::toUser($token);
		$userId = $user->id;
		return response()->json(compact('token', 'userId'));
	}

    public function ResetPassword(Request $request, $token=null) {
        $carbon = new Carbon();
        $date = $carbon->now();

        if ($request->has('email') && $request->has('password') && $request->has('passwordc') && $request->has('token_reset')) {

            $user =  User::where('email', $request->email)->first();
            $rpass = ResetPass::where('email', $user->email)->first();

            if (!is_null($rpass)) {
                if (!is_null($user) && (strcmp($request->password, $request->passwordc) == 0 )) {

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
                    $data['msg']    = 'Confirmacion de contraseña no coincide';
                }
            } else {
                $data['errors'] = true;
                $data['msg']    = 'La peticion de cambio de contraseña a caducado';
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
            $data['msg']    = 'Datos requeridos';
            
            return redirect(
                'sendmailreset'
            )->with('respuesta', $data);
        }
    }
}