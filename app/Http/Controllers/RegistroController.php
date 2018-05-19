<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\ResetPass;
use Illuminate\Http\Response as HttpResponse;
use DB;
use \Carbon\Carbon;
use Webpatser\Uuid\Uuid;
use App\Estadocuenta;
use App\Http\Controllers\Controller;
use App\Propiedad;
use App\PropiedadMoneda;
use App\TipoPropiedad;
use App\TipoHabitacion;
use App\User;
use App\Plan;
use App\QvoUser;
use App\Habitacion;
use App\DatosStripe;
use App\UbicacionProp;
use App\ZonaHoraria;
use App\PagoFacil;
use App\PasPago;
use App\PropiedadTipoDeposito;
use App\PagoOnline;
use Illuminate\Support\Facades\Event;
use Response;
use JWTAuth;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Cartalyst\Stripe\Stripe;
use Cartalyst\Stripe\Exception\MissingParameterException;
use \Illuminate\Database\QueryException;

class RegistroController extends Controller {

	public function SetEstado(Request $request) { 
		$validator = Validator::make(
			$request->all(), 
			array(
				'prop_id'       => 'required',
				'paso'       	=> 'required'
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

			$user = $propiedad->user->first();
			$user->update(["paso" => $request->paso]);

			$retorno['errors'] = false;
			$retorno['msg']    = "Paso modificado a: ".$user->paso;
		}
		return Response::json($retorno); 
	}

	public function signup(Request $request) { // paso 1
		$validator = Validator::make(
			$request->all(), 
			array(
				'email'       => 'required',
				'password'    => 'required',
				'url_retorno' => 'required',
				'name'		  => 'required',
				'phone'		  => 'required'
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
				$user 			 = new User();
				$user->paso  	 = 1;
				$user->email 	 = $request->email;
				$user->password  = $request->password;
				$user->name 	 = $request->name;
				$user->rol_id 	 = 1;
				$user->estado_id = 1;
				$user->phone 	 = $request->phone;
				$user->save();

				$propiedad 			  			= new Propiedad();
				$propiedad->nombre 	  			= "";
				$propiedad->direccion 			= "";
				$propiedad->ciudad 	  			= "";
				$propiedad->numero_habitaciones = 0;
				$propiedad->tipo_propiedad_id 	= 1;
				$propiedad->estado_cuenta_id 	= 3;
				$propiedad->zona_horaria_id 	= 176;
				$propiedad->codigo 				= (string) Uuid::generate(4);
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
				
				return Redirect::to("https://".$retorno.".com/#!/login");
			} else {
				return Redirect::to("https://".$retorno.".com/#!/login");
			} 
		} 
		return Response::json($retorno); 
	}

	public function SetEstadoCuenta(Request $request) {
		$validator = Validator::make(
			$request->all(), 
			array(
				'prop_id'       => 'required',
				'estado'      	=> 'required'
			)
		);

		if ($validator->fails()) {
			$retorno['errors'] = true;
			$retorno["msg"]    = $validator->errors();
		} else {
			if ($request->estado != 1 && $request->estado != 2 && $request->estado != 3) {
				$retorno['errors'] = true;
				$retorno["msg"]    = "Estado invalido. Opciones validas: \n 1)Prueba \n2)Activa \n3)Inactiva";
			} else {
				$propiedad = Propiedad::where(
					"id",
					$request->prop_id
				)->first();
				$propiedad->estado_cuenta_id = $request->estado;
				$propiedad->save();

				$retorno['errors'] = false;

				switch ($propiedad->estado_cuenta_id) {
					case 1:
						$retorno["msg"]    = "La cuenta se encuentra en un estado de prueba";
						break;

					case 2:
						$retorno["msg"]    = "La cuenta se encuentra activa";
						break;

					case 3:
						$retorno["msg"]    = "La cuenta se encuentra inactiva, y con restricciones";
						break;
					
					default:
						break;
				}
			}
		}
		return Response::json($retorno); 
	}

	public function getPaso(Request $request) {
		$validator = Validator::make(
			$request->all(), 
			array(
				'user_id'  => 'required'
			)
		);

		if ($validator->fails()) {
			$retorno['errors'] = true;
			$retorno["msg"]    = $validator->errors();
		} else {
			$user = User::where(
				"id",
				$request->user_id
			)->first();

			if (!is_null($user)) {
				$retorno['errors'] = false;
				$retorno["msg"]    = $user->paso;
			} else {
				$retorno['errors'] = true;
				$retorno["msg"]    = "Usuario no encontrado";
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
					if ($user->paso == 2) {
						$user->update(["paso" => 3]);
					}
					$paso = $user->paso;

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
				'prop_id'       	  	  => 'required',
				'nombre' 	  		  	  => 'required',
				'direccion' 	  	  	  => 'required',
				'ciudad' 	  		  	  => 'required',
				'tipo_propiedad_id'   	  => 'required',
				'pais_id' 	  	 	  	  => 'required',
				'telefono' 	  		  	  => 'required',
				'iva' 	  		      	  => 'required',
				'email' 	  		  	  => 'required',
				'tipo_cobro_id'       	  => 'required',
				'tipo_deposito_id'	  	  => 'required',
				'zona_horaria_id' 	  	  => 'required',
				'monedas' 				  => 'required',
				'longitud' 	  		  	  => 'required',
				'latitud' 	  		  	  => 'required'
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
			$propiedad->tipo_propiedad_id 	= $request->tipo_propiedad_id;
			$propiedad->pais_id 			= $request->pais_id;
			$propiedad->ciudad 				= $request->ciudad;
			$propiedad->direccion 			= $request->direccion;
			$propiedad->numero_habitaciones = 0;
			$propiedad->telefono 			= $request->telefono;
			$propiedad->iva 				= $request->iva;
			$propiedad->email 				= $request->email;

			if ($request->has('region_id')) {
				$propiedad->region_id 			= $request->region_id;
			}
			
			$propiedad->zona_horaria_id 	= $request->zona_horaria_id;
			$propiedad->estado_cuenta_id 	= 2;
			$propiedad->codigo 				= (string) Uuid::generate(4);
			$propiedad->tipo_cobro_id		= $request->tipo_cobro_id;
			$propiedad->save();

			$ubicacion 						= UbicacionProp::where(
				'prop_id',
				$request->prop_id
			)->first();

			if (is_null($ubicacion)) {
				$ubicacion           			= new UbicacionProp();
				$ubicacion->prop_id  			= $propiedad->id;
				$ubicacion->location 			= new Point(
					$request->longitud,
					$request->latitud 
				);
			} else {
				$ubicacion->location 			= new Point(
					$request->longitud,
					$request->latitud 
				);
			}
			$ubicacion->save();

			$tipo_deposito                   	 = PropiedadTipoDeposito::where(
				'propiedad_id',
				$request->prop_id
			)->first();

			if (!is_null($tipo_deposito)) {
				$tipo_deposito->valor            = $request->porcentaje_deposito;
	            $tipo_deposito->tipo_deposito_id = $request->tipo_deposito_id;
			} else {
				$tipo_deposito                   = new PropiedadTipoDeposito();
	            $tipo_deposito->propiedad_id     = $request->prop_id;
        	}

        	if ($request->has('porcentaje_deposito')) {
        		$tipo_deposito->valor            = $request->porcentaje_deposito;
        	} else {
        		$tipo_deposito->valor            = 0;
        	}
        	
	        $tipo_deposito->tipo_deposito_id = $request->tipo_deposito_id;
	        $tipo_deposito->save();

        	$request->merge([ 
				'propiedad_id' => $request->prop_id
			]);

        	$resp = app('App\Http\Controllers\PropiedadController')->ingresoMonedas(
				$request
			);

			if ($resp->getData()->errors == false) {
				$user = $propiedad->user->first();
				$user->update(["paso" => 4]);

				$retorno['errors'] = false;
				$retorno["msj"]    = "Datos propiedad configurados";
			} else {
				$retorno['errors'] = true;
				$retorno["msj"]    = $resp->getData()->msj;
			}
		}
		return Response::json($retorno); 
	}

	public function Getconfig(Request $request) { 
		$validator = Validator::make(
			$request->all(), 
			array(
				'prop_id'       	  => 'required'
			)
		);

		if ($validator->fails()) {
			$retorno['errors'] = true;
			$retorno["msj"]    = $validator->errors();
		} else {
			$propiedad = Propiedad::where(
				'id', 
				$request->prop_id
			)->with(
				'tipoPropiedad',
				'pais',
				'region',
				'zonaHoraria' ,
				'tipoMonedas', 
				'tipoCobro',
				'tipoDepositoPropiedad'
			)->first();

			if (!is_null($propiedad)) {
				$propiedad->ubicacion = collect(
					UbicacionProp::getLocation(
						$request->prop_id
					)
				);

				$retorno['errors'] = false;
				$retorno["msj"]    = $propiedad;
			} else {
				$retorno['errors'] = true;
				$retorno["msj"]    = "La propiedad no existe";
			}
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

	public function GetTiposHabitacion(Request $request) {
		$validator = Validator::make(
			$request->all(), 
			array(
				'prop_id' => 'required'
			)
		);

		if ($validator->fails()) {
			$retorno['errors'] = true;
			$retorno["msj"]    = $validator->errors();

			return Response::json($retorno); 
		} else {
			$tipos = TipoHabitacion::where(
				'propiedad_id', 
				$request->prop_id
			)->get();

			$retorno['errors'] = false;
			$retorno["msj"]    = $tipos;
		}
		return Response::json($retorno); 
	}

	public function ejm(Request $request) {
		$hab = Habitacion::where(
				'propiedad_id', 
				$request->prop_id
			)->get();

			$propiedad = Propiedad::findOrFail(
				$request->prop_id
			);
			$propiedad->numero_habitaciones = $hab->count();
			$propiedad->save();

		return Response::json($hab->count()); 
	}

	public function habitaciones(Request $request) { // paso 6
		$validator = Validator::make(
			$request->all(), 
			array(
				'tipos_de_hab'       	  => 'required',
				'total_habitaciones'      => 'required'
			)
		);
		$flag = false;

		if ($validator->fails()) {
			$retorno['errors'] = true;
			$retorno["msj"]    = $validator->errors();

			return Response::json($retorno); 
		} else {
			$propiedad = Propiedad::where(
				"id",
				$request->tipos_de_hab[0]["prop_id"]
			)->first();

			$hab = Habitacion::where(
				'propiedad_id', 
				$request->propiedad_id
			)->get();

			$flag = false;

			if ($propiedad->numero_habitaciones == 0) {
				$flag = true;
			} elseif ($request->total_habitaciones <= ($propiedad->numero_habitaciones - $hab->count())) {
				$flag = true;
			} else {
				$flag = false;
			}

			if ($flag == true) {
				foreach ($request->tipos_de_hab as $t_hab) {
					$request->merge([ 
						'cant_x_tipo' => $t_hab["cant_x_tipo"]
					]);
					$request->merge([ 
						'capacidad' => $t_hab["capacidad"]				
					]);
					$request->merge([ 
						'nombre' => $t_hab["nombre"]
					]);
				
					$request->merge([ 
						'propiedad_id' => $t_hab["prop_id"]
					]);

					$hab = Habitacion::where(
						'propiedad_id', 
						$request->propiedad_id
					)->get();

					$resp = app('App\Http\Controllers\TipoHabitacionController')->store(
						$request,
						true
					);

					if ($resp->getData()->errors == false) {
						$request->merge([ 
							'tipo_habitacion_id' => $resp->getData()->msg->id
						]);

						for ($i = 0; $i < $request->cant_x_tipo; $i++) { 
							$hab = Habitacion::where(
								'propiedad_id', 
								$request->propiedad_id
							)->get();
							$request->merge(['nombre' => ($hab->count() + 1)]);
							$resp = app('App\Http\Controllers\HabitacionController')->store(
								$request
							);
						}

						$retorno['errors'] = false;
						$retorno["msj"]    = "Operacion realizada con exito\n\n".$resp;

						$tipos = TipoHabitacion::where(
							'propiedad_id', 
							$request->prop_id
						)->get();

						foreach ($tipos as $tipo) {
							$tipo->habitaciones = Habitacion::where(
								'tipo_habitacion_id',
								$tipo->id
							)->get();
						}

						$retorno["tipos"] = $tipos;

						$propiedad = Propiedad::findOrFail(
							$request->propiedad_id
						);
						$propiedad->numero_habitaciones = $hab->count() + 1;
						$propiedad->save();

						$user = $propiedad->user->first();
						$user->update(["paso" => 6]);

						$flag = true;
					} else {
						$retorno['errors'] = true;
						$retorno["msj"]    = $resp->getData()->msg;
						$tipos = TipoHabitacion::where(
							'propiedad_id', 
							$request->prop_id

						)->get();

						foreach ($tipos as $tipo) {
							$tipo->habitaciones = Habitacion::where(
								'tipo_habitacion_id',
								$tipo->id
							)->get();
						}
						$retorno["tipos"] = $tipos;
					}
				}

				if ($flag == true) {
					return Response::json($retorno); 
				} else {
					return $retorno; 
				}
			} else {
				$retorno['errors'] = true;
				$retorno["msj"]    = "El numero de habitaciones que desea crear, excede el total permitido por la Propiedad";
				return Response::json($retorno);
			}
		}
	}

	public function getPagoFacil(Request $request) {
		$validator = Validator::make(
			$request->all(), 
			array(
				'prop_id'       	  => 'required'
			)
		);

		if ($validator->fails()) {
			$retorno = PagoFacil::all();
		} else {
			$pago_o = PagoOnline::where(
				"prop_id",
				$request->prop_id
			)->first();

			$pago = PagoFacil::where(
				"pago_id",
				$pago_o->id
			)->get();

			$pago_o->detalle = $pago;
			$retorno = $pago_o;
		}
		return Response::json($retorno); 
	}

	public function getPlanes(Request $request) {
		$planes = Plan::all();
		return Response::json($planes); 
	}

	public function getPasarelas(Request $request) {
		$pasarelas = PasPago::all();
		$retorno['nacionales'] = PasPago::where(
			'procedencia', 
			'nacional'
		)->get();
		$retorno['internacionales'] = PasPago::where(
			'procedencia', 
			'internacional'
		)->get();
		return Response::json($retorno); 
	}

	public function getPagos(Request $request) {
		$pagos = PagoOnline::with('pas_pago')->get();

		$propiedad = Propiedad::findOrFail(
			$request->prop_id
		);

		$zona = $propiedad->zonaHoraria->first();

		foreach ($pagos as $pago) {
			$pago->plan = Plan::where(
				'plan_id', 
				$pago->plan_id
			)->first();
		}
		return Response::json($pagos); 
	}

	public function ejm_f(Request $request) {
		$fecha_actual = Carbon::now()->setTimezone('America/Santiago');
		$resp["uno"] = $fecha_actual;
		$fecha_actual2 = Carbon::now()->setTimezone('America/Santiago')->addMonths(1);
		$resp["dos"] = $fecha_actual2;
		return Response::json($resp); 
	}

	public function PropCero(Request $request) {
		$users = User::where(
			"paso",
			$request->pas
		)->get();

		if ($users->count() != 0) {
			foreach ($users as $user) {	
				$propiedad = $user->propiedad->first();
				
				if (!is_null($propiedad)) {
					$propiedad->zona_horaria_id = 176;
					$propiedad->save();

					$request->merge(['estado' 	   => 0]);
					$request->merge(['pas_pago_id' => 1]);
					$request->merge(['prop_id' 	   => $propiedad->id]);
					$request->merge(['plan_id' 	   => 4]);

					$resp = app('App\Http\Controllers\RegistroController')->SeleccionPago(
						$request
					);

					$us = User::find($user->id);
					$us->paso = $request->pasto;
					$us->save();			
				}
			}
		}
		
		$retorno["listo"] = "actualizado";
		return Response::json($retorno);
	}

	public function SeleccionPago(Request $request) { // paso 7
		$validator = Validator::make(
			$request->all(), 
			array( 
				'estado'		 	=> 'required',
				'pas_pago_id'		=> 'required',
				'prop_id'			=> 'required',
				'interval_time'		=> 'required',
				'plan_id'			=> 'required'
			)
		);

		if ($validator->fails()) {
			$retorno['errors'] = true;
			$retorno["msj"]    = $validator->errors();
			return Response::json($retorno); 
		} else {
			$propiedad = Propiedad::findOrFail(
				$request->prop_id
			);

			$zona = $propiedad->zonaHoraria->first();

			$fecha_actual = Carbon::now()->setTimezone(
				$zona->nombre
			);

			$pago = PagoOnline::where(
				"prop_id", 
				$request->prop_id
			)->first();

			if (is_null($pago)) {
				$pago = new PagoOnline();
			}

			$pago->fecha_facturacion  = $fecha_actual;
			$plan = Plan::find($request->plan_id);

			if ($propiedad->numero_habitaciones >= 37) {
	    		$habitaciones = 37;
	    	} else {
	    		$habitaciones = $propiedad->numero_habitaciones;
	    	}

			switch ($request->plan_id) {
                case 1: //mensual
                	$aux = (1 * $request->interval_time);
                  	$monto = $plan->precio_x_habitacion * $habitaciones;
                    $fecha_actual2 = Carbon::now()->setTimezone(
                        $zona->nombre
                    )->addMonths($aux);
                    break;

                case 2: //semestral
                	$aux = (6 * $request->interval_time);
                	$monto_s_base   = $plan->precio_x_habitacion * $habitaciones;
                  	$precio_mensual = $monto_s_base - (($monto_s_base * 3) / 100);
                  	$monto   = $precio_mensual * 6;
                    $fecha_actual2  = Carbon::now()->setTimezone(
                        $zona->nombre
                    )->addMonths($aux);
                    break;

                case 3: //anual
                	$aux = (1 * $request->interval_time);
                	$monto_a_base = $plan->precio_x_habitacion * $habitaciones;
                  	$precio_mensual = ($monto_a_base - (($monto_a_base * 10) / 100)) ;
                  	$monto = $precio_mensual * 12;
                    $fecha_actual2 = Carbon::now()->setTimezone(
                        $zona->nombre
                    )->addYear($aux);
                    break;
                
                default: //gratis
                	$aux = (1 * $request->interval_time);
                    $monto = $plan->precio_x_habitacion * $habitaciones;
                    $fecha_actual2 = Carbon::now()->setTimezone(
                        $zona->nombre
                    )->addMonths($aux);
                    break;
            }

            if (!is_null($pago)) {
        		$fecha_actual2_anterior = new Carbon(
                    $pago->prox_fac, 
                    $zona->nombre
                );
            	
            	$fecha_actual2->addDays(
            		$fecha_actual2_anterior->diffInDays($fecha_actual2)
            	);
            }

			$pago->estado 		= 0;
	    	$pago->prox_fac 	= $fecha_actual2;
	    	$pago->pas_pago_id 	= $request->pas_pago_id;
	    	$pago->prop_id 		= $request->prop_id;
	    	$pago->plan_id 		= $request->plan_id;
	    	$pago->save();

	    	if ($monto == 0) {
	    		$retorno['errors'] = false;
				$retorno["msj"]    = "Es plan gratuito ha sido seleccionado con exito";
				return Response::json($retorno); 
	    	} else {
	    		$request->merge([ 
					'monto'   => $monto
				]);
				$request->merge([ 
					'email'   => $propiedad->email
				]);

				$user = $propiedad->user->first();
				$user->update(["paso" => 7]);

		    	$resp = app('App\Http\Controllers\PagoFacilController')->Trans(
					$request
				);
				return $resp;
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