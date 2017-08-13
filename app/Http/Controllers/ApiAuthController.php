<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\User;
use App\Estadocuenta;
use Illuminate\Http\Response as HttpResponse;
use Response;
use DB;

class ApiAuthController extends Controller {

	public function signin(Request $request) {
        $credentials = $request->only('email', 'password');
        $user = User::where('email', $credentials['email'])->first();
        $user_id = $user->id;

        if(!is_null($user)) {
        	switch ($user->propiedad->EstadoCuenta->estado) {
        		case '2': //
        			$status         = 400;
                    $data['errors'] = true;
                    $data['msg']    = 'Su cuenta de prueba a caducado';
        		break;

    			default:
                    if (!$token = JWTAuth::attempt($credentials)) {
                        $data['errors'] = true;
                        $data['msg']    = 'Usuario o contraseña incorrecta';
                        $status = HttpResponse::HTTP_FORBIDDEN;
                    } else {
                        $status          = 200;
                        $data['errors']  = false;
                        $data['msg']     = 'Usuario correcto';
                        $data['usuario'] = compact('token', 'user_id');
                    }
        		break;
        	}
        } else {
        	$data['errors'] = true;
        	$data['msg']  	= 'Usuario o contraseña incorrecta';
            $status = HttpResponse::HTTP_FORBIDDEN;
        } 
        return Response::json($data, $status); 
    }

	public function userAuth(Request $request){
		$credentials = $request->only('email', 'password');
		$token = null;

		try{
			if(!$token = JWTAuth::attempt($credentials)){
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
		/*$userProp = DB::table('propiedades')->where('user_id', $user->id)->value('nombre');*/

		/*return response()->json(compact('token', 'user', 'userProp'));*/
		return response()->json(compact('token', 'userId'));
	}
}