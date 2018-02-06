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

class RegistroController extends Controller {

	public function FunctionName($value='') {
		# code...
	}

    public function signin(Request $request) {
        $credentials = $request->only('email', 'password');
        $user = User::where(
        	'email', 
        	$credentials['email']
        )->with('propiedad')
        ->first();

        if(!is_null($user)) {
            $user_id = $user->id;
            $propiedad_id = $user->propiedad[0]['id'];

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
        	$data['msg']  	= trans('request.failure.bad');
            $status         = trans('request.failure.code.not_founded');
        } 
        return Response::json($data, $status); 
    }
}