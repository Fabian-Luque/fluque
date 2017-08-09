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

        if(!is_null($user)) {
        	$estado = Estadocuenta::where('propiedad_id', 2)->first();

        	if (!is_null($estado)) {
        		switch ($estado->estado) {
        			case '2':
        				dd("hola");
        				break;
        			
        			default:
        			if (!$token = JWTAuth::attempt($credentials)) {
            		$data['errors'] = true;
                	$data['msg']  	= 'Usuario o contraseña incorrecta';
            		$status = HttpResponse::HTTP_FORBIDDEN;
        		} 
        				break;
        		}
        	} else {
        		$status			= 400;
        		$data['errors'] = true;
        		$data['msg']  	= 'Su cuenta a caducado';
        	}      
        } else {
        	$data['errors'] = true;
        	$data['msg']  	= 'xxxUsuario o contraseña incorrecta';
            $status = HttpResponse::HTTP_FORBIDDEN;
        } 
        return Response::json($data, $status);
        $user_id = $user->id; 
        return Response::json(compact('token', 'user_id'), 201);
    }

	public function userAuth(Request $request){
		$credentials = $request->only('email', 'password');
		$token = null;

		try{
			if(!$token = JWTAuth::attempt($credentials)){
				return response()->json(['error' => 'invalid_credentials'], 401);
			}
		} catch(JWTException $ex) {
			return response()->json(['error' => 'algo anda mal'], 500);
		}

		$user = JWTAuth::toUser($token);
		$userId = $user->id;
		/*$userProp = DB::table('propiedades')->where('user_id', $user->id)->value('nombre');*/

		/*return response()->json(compact('token', 'user', 'userProp'));*/
		return response()->json(compact('token', 'userId'));
	}
}