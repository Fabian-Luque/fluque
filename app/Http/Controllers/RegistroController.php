<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
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
use Webpatser\Uuid\Uuid;

class RegistroController extends Controller {

	public function signup(Request $request) { // paso 1
		return 
		$validator = Validator::make(
			$request->all(), 
			array(
				'email'       => 'required',
				'password'    => 'required',
				'url_retorno' => 'required'
			)
		);

		if ($validator->fails()) {
			$retorno['errors'] = true;
			$retorno["msj"]    = $validator->errors();
		} else {
			$credentials = $request->only(
				'email', 'password'
			);
			$user = User::where(
				'email', 
				$credentials['email']
			)->first();

			if(is_null($user)) {
				$user 			= new User();
				$user->paso  	= 1;
				$user->email 	= $request->email;
				$user->password = $request->password;
				$user->save();

				$propiedad 			  			= new Propiedad();
				$propiedad->nombre 	  			= "";
				$propiedad->direccion 			= "";
				$propiedad->ciudad 	  			= "";
				$propiedad->numero_habitaciones = 0;
				$propiedad->tipo_propiedad_id 	= 1;
				$propiedad->estado_cuenta_id 	= 3;
				$propiedad->save();

				$user->propiedad()->attach($propiedad->id);

				$arr = array(
					'user'  	  => $user->email,
					'pass'  	  => $request->password,
					'token' 	  => JWTAuth::attempt($credentials),
					'de'    	  => 'Gofeels',
					'url'   	  => url(''),
					'url_retorno' => $request->url_retorno,
					'comp'  	  => 0
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

	public function comprobar($email, $retorno, $token=null) { // paso 2
		if ($token != null) {
			$user = User::where(
				'email', 
				$email
			)->first();

			if(!is_null($user) && $user->paso == 1) {
				$user->paso = 2;
				$user->save();
				
				return Redirect::to("https://".$retorno."/#!/login");
			} else {
				return Redirect::to("https://".$retorno."/#!/login");
			} 
		} 
		return Response::json($retorno); 
	}

	public function signin(Request $request) { // paso 3
		$validator = Validator::make(
			$request->all(), 
			array(
				'email'       => 'required',
				'password' 	  => 'required'
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
					$retorno['msg']    = 'Usuario o contraseÃ±a incorrecta';
					$status            = trans('request.failure.code.forbidden');
				} else {
					$user->update(["paso" => 3]);
					$paso 		  = $user->paso;

					$retorno = compact(
						'token', 
						'user_id', 
						'propiedad_id', 
						'paso'
					);
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

	public function configurar(Request $request) { // paso 4
		$validator = Validator::make(
			$request->all(), 
			array(
				'prop_id'       	  => 'required',
				'nombre' 	  		  => 'required',
				'direccion' 	  	  => 'required',
				'ciudad' 	  		  => 'required',
				'numero_habitaciones' => 'required',
				'tipo_propiedad_id'   => 'required',
				'pais_id' 	  	 	  => 'required',
				'telefono' 	  		  => 'required',
				'nombre_responsable'  => 'required',
				'iva' 	  		      => 'required',
				'email' 	  		  => 'required',
				'region_id' 	  	  => 'required',
				'porcentaje_deposito' => 'required',
				'estado_cuenta_id' 	  => 'required',
				'longitud' 	  		  => 'required',
				'latitud' 	  		  => 'required'
			)
		);

		if ($validator->fails()) {
			$retorno['errors'] = true;
			$retorno["msj"]    = $validator->errors();
		} else {
			$propiedad = Propiedad::where(
				'id', 
				$request->prop_id
			)->first();

			$propiedad->nombre 				= $request->nombre;
			$propiedad->direccion 			= $request->direccion;
			$propiedad->ciudad 				= $request->ciudad;
			$propiedad->numero_habitaciones = $request->numero_habitaciones;
			$propiedad->tipo_propiedad_id 	= $request->tipo_propiedad_id;
			$propiedad->pais_id 			= $request->pais_id;
			$propiedad->ciudad 				= $request->ciudad;
			$propiedad->direccion 			= $request->direccion;
			$propiedad->telefono 			= $request->telefono;
			$propiedad->nombre_responsable 	= $request->nombre_responsable;
			$propiedad->iva 				= $request->iva;
			$propiedad->email 				= $request->email;
			$propiedad->region_id 			= $request->region_id;
			$propiedad->zona_horaria_id 	= $request->zona_horaria_id;
			$propiedad->estado_cuenta_id 	= 2;
			$propiedad->codigo 				= (string) Uuid::generate(4);
			$propiedad->save();

			$ubicacion           			= new UbicacionProp();
			$ubicacion->prop_id  			= $propiedad->id;
			$ubicacion->location 			= new Point(
				$request->longitud,
				$request->latitud 
			);
			$ubicacion->save();

			$user = $propiedad->user->first();
			$user->update(["paso" => 4]);

			$retorno['errors'] = false;
			$retorno["msj"]    = "Datos propiedad configurados";
		}
		return Response::json($retorno); 
	}

	public function calendario(Request $request) { // paso 5
		$retorno = app('App\Http\Controllers\TemporadaController')->calendario(
			$request
		);

		if ($retorno->getData()->errors == false) {
			$propiedad_id = $request->propiedad_id;
			$propiedad    = Propiedad::where(
				'id', 
				$propiedad_id
			)->first();

			$user = $propiedad->user->first();
			$user->update(["paso" => 5]);
		} 
		return $retorno; 
	}

	public function habitaciones(Request $request) { // paso 6
		$retorno = app('App\Http\Controllers\TipoHabitacionController')->index(
			$request
		);

		try {
			$retorno->getData();
			return $retorno;
		} catch (\BadMethodCallException $e) {
			if (count($retorno) > 0) {
				$retorno = app('App\Http\Controllers\HabitacionController')->store(
					$request
				);

				if ($retorno->getData()->errors == false) {
					$propiedad_id = $request->propiedad_id;
					$propiedad    = Propiedad::where(
						'id', 
						$propiedad_id
					)->first();

					$user = $propiedad->user->first();
					$user->update(["paso" => 6]);
				} 
				return $retorno;
			} else {
				$retorno['errors'] = true;
				$retorno['msg']	   = "Debe antes registrar al menos un tipo de habitacion";

				return Response::json($retorno); 
			}
		}
	}

	public function stripe(Request $request) { // paso 7
		$retorno = app('App\Http\Controllers\StripeController')->PlanStripeCrear(
			$request
		);

		//if ($retorno->getData()->errors == false) {
		//}
		
		return $retorno;
	}
}