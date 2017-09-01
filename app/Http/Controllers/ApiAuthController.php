<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\User;
use Illuminate\Http\Response as HttpResponse;
use Response;
use DB;

class ApiAuthController extends Controller
{




	public function signin(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if ($user->estado_id == 1) {
	        if (!$token = JWTAuth::attempt($credentials)) {
	            $data = [
	                'errors' => true,
	                'msg'    => 'Usuario o contraseña incorrecta',
	            ];
	            return Response::json($data, HttpResponse::HTTP_FORBIDDEN);
	        }
        } else {

        	$data = array(
                'msg'    => "Su cuenta se encuenta inactiva",
                'errors' => true);
            return Response::json($data, 401);
        }

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


		}catch(JWTException $ex){

			return response()->json(['error' => 'algo anda mal'], 500);


		}

		$user = JWTAuth::toUser($token);

		$userId = $user->id;


		/*$userProp = DB::table('propiedades')->where('user_id', $user->id)->value('nombre');*/

		/*return response()->json(compact('token', 'user', 'userProp'));*/
		return response()->json(compact('token', 'userId'));


		}






	}





