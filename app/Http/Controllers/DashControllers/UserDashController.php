<?php

namespace App\Http\Controllers\DashControllers;

use App\Estadocuenta;
use App\Http\Controllers\Controller;
use App\Propiedad;
use App\TipoPropiedad;
use App\User;
use App\QvoUser;
use App\DatosStripe;
use App\UbicacionProp;
use App\ZonaHoraria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Response;
use JWTAuth;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Jobs\ProcesoQVO;
use Illuminate\Support\Facades\Validator;
use Cartalyst\Stripe\Stripe;

class UserDashController extends Controller {

    public function CreateUser(Request $request) {
        if ($request->has('name') && $request->has('email') && $request->has('password') && $request->has('phone') && $request->has('nombre') && $request->has('direccion') && $request->has('tipo_propiedad_id') && $request->has('tipo_cuenta') && $request->has('ciudad') && $request->has('numero_habitaciones') && $request->has('latitud') && $request->has('longitud') && $request->has('periodo')) {
            $us = User::where('email',$request->email)->first();
            if (!isset($us->email)) {
                $codigo = str_random(50);
                $prop   = Propiedad::where('codigo', $codigo)->first();

                if (is_null($prop)) {

                    if ($request->has('dias_prueba')) {
                        $dias_prueba = $request->dias_prueba;
                    } else {
                        $dias_prueba = 15;
                    }

                    $usuario = new User();
                    $usuario->name = $request->name;
                    $usuario->email = $request->email;
                    $usuario->password = $request->password;
                    $usuario->phone = $request->phone;
                    $usuario->rol_id = 1;
                    $usuario->estado_id = 1;
                    $usuario->save();

                    $propiedad = new Propiedad();
                    $propiedad->nombre = $request->nombre;
                    $propiedad->direccion = $request->direccion;
                    $propiedad->ciudad = $request->ciudad;
                    $propiedad->numero_habitaciones = $request->numero_habitaciones;
                    $propiedad->tipo_propiedad_id = $request->tipo_propiedad_id;
                    $propiedad->estado_cuenta_id = $request->tipo_cuenta;
                    $propiedad->codigo = $codigo;
                    $propiedad->save();

                    $usuario->propiedad()->attach($propiedad->id);

                    $ubicacion           = new UbicacionProp();
                    $ubicacion->prop_id  = $propiedad->id;
                    $ubicacion->location = new Point(
                        $request->latitud, 
                        $request->longitud
                    );
                    $ubicacion->save();

                    $stripe = Stripe::make(config('app.STRIPE_SECRET'));

                    $plan = $stripe->plans()->create([
                        'id'                   => $usuario->email.'_'.$propiedad->nombre,
                        'name'                 => $propiedad->nombre,
                        'amount'               => config('app.PRECIO_X_HAB_QVO') * $propiedad->numero_habitaciones,
                        'currency'             => 'USD',
                        'interval'             => $request->periodo,
                        'trial_period_days'    => '15',
                        'interval_count'       => $request->intervalo,
                    ]);

                    $datos_stripe = new DatosStripe();
                    $datos_stripe->plan_id = $plan['id'];
                    $datos_stripe->save();

                    $data['accion'] = 'Crear usuario';
                    $data['msg'] = 'Usuario creado exitosamente';
                } else {
                    $status            = trans('request.failure.code.bad_request');
                    $retorno['errors'] = true;
                    $retorno['msj']    = "Error al intentar crear la cuenta";
                }
            } else {
                $data['accion'] = 'Crear usuario';
                $data['msg'] = 'Error. El correo ingresado ya esta en uso';
            }
        } else {
            $data['accion'] = 'Crear usuario';
            $data['msg'] = 'Datos requeridos';
        }
        return redirect('dash/adminuser')->with(
            'respuesta', 
            $data
        );
    }

    public function ReadUser(Request $request) {  
        if ($request->has('id')) {
            $user = User::where(
                'id',
                $request->id
            )->with('propiedad')->first();

            if (count($user) != 0) {
                $data['errors'] = false;
                $data['msg']    = $user;
            } else {
                $data['errors'] = true;
                $data['msg']    = 'Usuario no encontrado';
            }
            return Response::json($data);
        } else {
            $data = User::all();

            return View('administrador.user')->with(
                'users', 
                $data
            );
        }
    }

    public function UpdateUser(Request $request)  {
        $rules = array(
            'nombre'     => '',
            'direccion'     => '',
            'ciudad'     => '',
            'numero_habitaciones'     => 'numeric',
            'tipo_propiedad_id'     => 'numeric',
            'estado_cuenta_id'     => 'numeric',
            'name'     => '',
            'email'    => 'email',
            'phone'    => '',
            'rol_id'   => 'numeric',
            'estado_id'=> 'numeric',
            'latitud'  => 'numeric',
            'longitud' => 'numeric',
        );

        $validator = Validator::make($request->all(), $rules);
        $data['accion'] = 'Actualizar Registro';

        if (!$validator->fails()) {
            $usuario = User::find($request->id);
            if (!isset($us->email)) {
                $usuario->update($request->all());
                $propiedad = Propiedad::find($request->id);
                $propiedad->update($request->all());    
                
                $data['msg'] = 'Registro Actualizado Satisfactoriamente';
            } else {
                $data['msg'] = 'Error. El correo ingresado ya esta en uso';
            }
        } else {
            $data['msg'] = "";
            foreach ($validator->messages()->all() as $msg) {
                $data['msg'] .= $msg;
            }
        }
        return redirect('dash/adminuser')->with(
            'respuesta', 
            $data
        );
    }  

    public function DeleteUser(Request $request) {
        if ($request->has('id')) {
            if ($user = User::find($request->id)) {
                $user->delete();

                $data['errors'] = false;
                $data['msg']    = 'Usuario eliminado satisfactoriamente';
            } else {
                $data['errors'] = true;
                $data['msg']    = 'Usuario no encontrado';
            }
        } else {
            $data['errors'] = true;
            $data['msg']    = 'Datos requeridos';
        }
        return Response::json($data);
    } 

    public function getViewTipoPropiedad(Request $request) {
        $TipoPropiedad = TipoPropiedad::all();
        return View('administrador.reguser')->with(
            'tprops', 
            $TipoPropiedad
        );
    }

    public function getViewPropiedad(Request $request) {
        $propiedades = Propiedad::all(); 
        setlocale(LC_ALL,"es_CO.utf8");   

        foreach ($propiedades as $prop) {    
            $aux = str_replace(":", " ", $prop->created_at);
            $aux = str_replace("-", " ", $aux);
            $arr = (explode(' ', $aux));
       
            $prop->created = strftime(
                "%A %d de %B del %Y",
                mktime(
                    $arr[5],
                    $arr[4],
                    $arr[3],
                    $arr[2],
                    $arr[1],
                    $arr[0]
                )
            );

            if (isset($prop->QVO)) {
                $prop->qvo = true;
            } else {
                $prop->qvo = false;
            }

            if (isset($prop)) {
                $prop->tipo_propiedad = TipoPropiedad::find(
                    $prop->tipo_propiedad_id
                )->nombre;
                $prop->tipo_propiedad = trim($prop->tipo_propiedad);
            }
            if (isset($prop->estado_cuenta_id)) {
                $prop->estado_cuenta = Estadocuenta::find(
                    $prop->estado_cuenta_id
                )->nombre;
                $prop->estado_cuenta = trim($prop->estado_cuenta);
            }

            $prop->id = trim($prop->id);
            $prop->nombre = trim($prop->nombre);
            $prop->numero_habitaciones = trim($prop->numero_habitaciones);
            $prop->ciudad = trim($prop->ciudad);
            $prop->direccion = trim($prop->direccion);
            $prop->created = trim($prop->created);
        }
        return View('administrador.prop')->with(
            'props', 
            $propiedades
        );
    }




    public function CreateUserP(Request $request) {
        if ($request->has('name') && $request->has('email') && $request->has('password') && $request->has('phone') && $request->has('nombre') && $request->has('direccion') && $request->has('tipo_propiedad_id') && $request->has('tipo_cuenta') && $request->has('ciudad') && $request->has('numero_habitaciones') && $request->has('latitud') && $request->has('longitud') && $request->has('periodo')) {
            $us = User::where('email',$request->email)->first();
            if (!isset($us->email)) {
                $codigo = str_random(50);
                $prop   = Propiedad::where('codigo', $codigo)->first();

                if (is_null($prop)) {

                    if ($request->has('dias_prueba')) {
                        $dias_prueba = $request->dias_prueba;
                    } else {
                        $dias_prueba = 15;
                    }

                    $usuario = new User();
                    $usuario->name = $request->name;
                    $usuario->email = $request->email;
                    $usuario->password = $request->password;
                    $usuario->phone = $request->phone;
                    $usuario->rol_id = 1;
                    $usuario->estado_id = 1;
                    $usuario->save();

                    $propiedad = new Propiedad();
                    $propiedad->nombre = $request->nombre;
                    $propiedad->direccion = $request->direccion;
                    $propiedad->ciudad = $request->ciudad;
                    $propiedad->numero_habitaciones = $request->numero_habitaciones;
                    $propiedad->tipo_propiedad_id = $request->tipo_propiedad_id;
                    $propiedad->estado_cuenta_id = $request->tipo_cuenta;
                    $propiedad->codigo = $codigo;
                    $propiedad->save();

                    $usuario->propiedad()->attach($propiedad->id);

                    $ubicacion           = new UbicacionProp();
                    $ubicacion->prop_id  = $propiedad->id;
                    $ubicacion->location = new Point(
                        $request->latitud, 
                        $request->longitud
                    );
                    $ubicacion->save();

                    $user = $usuario;
                    $client = new Client();

                    $data['accion'] = 'Crear usuario';
                    $data['msg'] = 'Usuario creado exitosamente';

                    if (!is_null($propiedad->id)) {
                        try {
                            $body = $client->request(
                                'POST',  
                                config('app.qvo_url_base').'/customers', [
                                    'json' => [
                                        'email' => $propiedad->user[0]->email,
                                        'name'  => $propiedad->nombre
                                    ],
                                    'headers' => [
                                        'Authorization' => 'Bearer '.config('app.qvo_key')
                                    ]
                                ]
                            )->getBody();
    
                            $response = json_decode($body);
                            $retorno['errors'] = false;
                            $retorno["msj"] = $response;

                            $qvo_user = new QvoUser();
                            $qvo_user->prop_id = $propiedad->id;
                            $qvo_user->qvo_id  = $response->id;
                            $qvo_user->save();

                            try {
                                $body = $client->request(
                                    'POST', 
                                    config('app.DOLAR_PRICE_API')
                                )->getBody();
            
                                $dolar_price = json_decode($body); 
                                $dolar_price = intval($dolar_price->quotes->USDCLP);
                            } catch (GuzzleException $e) {
                                $dolar_price = 600;
                            }

                            if ($propiedad->numero_habitaciones > 27) {
                                $precio = round($dolar_price * 27);    
                            } else {
                                $precio = round(
                                    $dolar_price * $propiedad->numero_habitaciones
                                );
                            }

                            try {
                                $body = $client->request(
                                    'POST',  
                                    config('app.qvo_url_base').'/plans', [
                                        'json' => [
                                            'id' => $propiedad->id,
                                            'name' => $propiedad->nombre,
                                            'price' => $precio * intval(
                                                config('app.PRECIO_X_HAB_QVO')
                                            ),
                                            'currency' => 'CLP',
                                            'interval' => 'year',
                                            'trial_period_days' => $dias_prueba
                                        ],
                                        'headers' => [
                                            'Authorization' => 'Bearer '.config('app.qvo_key')
                                        ]
                                    ]
                                )->getBody();

                                $response = json_decode($body);

                                try {
                                    $body = $client->request(
                                        'POST', 
                                        config('app.qvo_url_base').'/subscriptions', [
                                            'json' => [
                                                'customer_id' => $qvo_user->qvo_id,
                                                'plan_id' => $propiedad->id
                                            ],
                                            'headers' => [
                                                'Authorization' => 'Bearer '.config('app.qvo_key')
                                            ]
                                        ]
                                    )->getBody();
                                    $response = json_decode($body);

                                    $qvo_user->solsub_id = $response->id;
                                    $qvo_user->save(); 

                                    $retorno['errors'] = false;
                                    $retorno["msj"]    = $response;
                                } catch (GuzzleException $e) {
                                    $status            = trans('request.failure.code.bad_request');
                                    $retorno['errors'] = true;
                                    $retorno["msj"]    = json_decode(
                                        (string)$e->getResponse()->getBody()
                                    );
                                } 
                            } catch (GuzzleException $e) {
                                $status            = trans('request.failure.code.bad_request');
                                $retorno['errors'] = true;
                                $retorno["msj"] = json_decode(
                                    (string)$e->getResponse()->getBody()
                                );
                            }  
                        } catch (GuzzleException $e) {
                            $status = trans('request.failure.code.bad_request');
                            $retorno['errors'] = true;
                            $retorno["msj"] = json_decode(
                                (string)$e->getResponse()->getBody()
                            );
                        } 
                    } else {
                        $status            = trans('request.failure.code.bad_request');
                        $retorno['errors'] = true;
                        $retorno['msj']    = "El usuario no se encuentra registrado";
                    }
                } else {
                    $status            = trans('request.failure.code.bad_request');
                    $retorno['errors'] = true;
                    $retorno['msj']    = "Error al intentar crear la cuenta";
                }
            } else {
                $data['accion'] = 'Crear usuario';
                $data['msg'] = 'Error. El correo ingresado ya esta en uso';
            }
        } else {
            $data['accion'] = 'Crear usuario';
            $data['msg'] = 'Datos requeridos';
        }
        return Response::json($data);
    }
}