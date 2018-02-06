<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Requests;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\User;
use App\Propiedad;
use App\ResetPass;
use App\Estadocuenta;
use Illuminate\Http\Response as HttpResponse;
use Response;
use DB;
use \Carbon\Carbon;

class RegistroController extends Controller {

	public function signup(Request $request) {
		$validator = Validator::make(
        	$request->all(), 
        	array(
            	'email'    => 'required',
            	'password' => 'required',
           	)
        );

        if ($validator->fails()) {
        	$retorno['errors'] = true;
        	$retorno["msj"]    = $validator->errors();
        } else {
			$credentials = $request->only('email', 'password');
	        $user = User::where(
	        	'email', 
	        	$credentials['email']
	        )->with('propiedad')
	        ->first();

	        if(is_null($user)) {
	        	$user 			= new User();
	        	$user->paso  	= 1;
	        	$user->email 	= $request->email;
	        	$user->password = $request->password;
	        	$user->save();

	        	$propiedad = new Propiedad();

	        	$arr = array(
                    'user'  => $user->email,
                    'pass'  => $request->password,
                    'token' => JWTAuth::attempt($credentials),
                    'de'    => 'Gofeels',
                    'comp'  => 0
                );

	        	$this->EnvioCorreo(
                    $propiedad,
                    $user->email,
                    $arr,
                    "correos.bienvenida2",
                    "",
                    "",
                    1,
                    "",
                    ""
                );

	            $retorno['errors'] = false;
	        	$retorno['msg']    = "El usuario ha sido registrado satisfactoriamente";
	        } else {
	        	$retorno['errors'] = true;
	        	$retorno['msg']    = "El usuario ya se encuentra registrado";
	        } 
	    }
        return Response::json($retorno); 
	}

	public function comprobar($correo) {
		# code...
	}

    public function signin(Request $request) {
    	$validator = Validator::make(
        	$request->all(), 
        	array(
            	'email'    => 'required',
            	'password' => 'required',
           	)
        );

        if ($validator->fails()) {
        	$retorno['errors'] = true;
        	$retorno["msj"]    = $validator->errors();
        	$status            = trans('request.failure.code.forbidden');
        } else {
	        $credentials = $request->only('email', 'password');
	        $user = User::where(
	        	'email', 
	        	$credentials['email']
	        )->with('propiedad')
	        ->first();

	        if(!is_null($user)) {
	            $user_id 	  = $user->id;
	            $propiedad_id = $user->propiedad[0]['id'];

	            if (!$token = JWTAuth::attempt($credentials)) {
	                $retorno['errors'] = trans('request.failure.status');
	                $retorno['msg']    = 'Usuario o contraseña incorrecta';
	                $status            = trans('request.failure.code.forbidden');
	            } else {
	                $retorno = compact('token', 'user_id', 'propiedad_id');
	                $status  = trans('request.success.code');
	            }
	        } else {
	        	$retorno['errors'] = trans('request.failure.status');
	        	$retorno['msg']    = trans('request.failure.bad');
	            $status            = trans('request.failure.code.not_founded');
	        } 
	    }
        return Response::json($retorno, $status); 
    }
}