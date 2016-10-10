<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Tymon\JWTAuth\Facades\JWTAuth;

class ApiAuthController extends Controller
{
    



	public function userAuth(Request $request){

		$credentials = $request->only('email', 'password');
		$token = null;

		try{

			if(!$token = JWTAuth::attempt($credentials)){
				return response()->json(['error' => 'invalid_credentials']);


			}


		}catch(JWTException $ex){

			return response()->json(['error' => 'algo anda mal'], 500);


		}

		$user = JWTAuth::toUser($token);

		return response()->json(compact('token', 'user'));


		}




	}





