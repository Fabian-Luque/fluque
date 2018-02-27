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
use Illuminate\Support\Facades\Validator;
use Cartalyst\Stripe\Stripe;
use Cartalyst\Stripe\Exception\MissingParameterException;
use \Illuminate\Database\QueryException;
use Webpatser\Uuid\Uuid;

class UserDashController extends Controller {

    public function CreateUser(Request $request) {
        $validator = Validator::make(
            $request->all(), 
            array(
                'name'                => 'required',
                'email'               => 'required',
                'password'            => 'required',
                'phone'               => 'required',
                'direccion'           => 'required',
                'tipo_propiedad_id'   => 'required',
                'tipo_cuenta'         => 'required',
                'numero_habitaciones' => 'required',
                'latitud'             => 'required',
                'longitud'            => 'required'
            )
        );

        if ($validator->fails()) {
            $retorno['errors'] = true;
            $retorno["msj"] = $validator->errors();
        } else {
            $us = User::where('email',$request->email)->first();
            if (!isset($us->email)) {
                $codigo = (string) Uuid::generate(4);
                $prop   = Propiedad::where(
                    'codigo', 
                    $codigo
                )->first();

                if (is_null($prop)) {
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
                        $request->longitud,
                        $request->latitud 
                    );
                    $ubicacion->save();

                    $arr = array(
                        'user' => $request->email,
                        'pass' => $request->password,
                        'de'   => 'Gofeels' 
                    );

                    $this->EnvioCorreo(
                        $propiedad,
                        $request->email,
                        $arr,
                        "correos.bienvenida",
                        "",
                        "",
                        1,
                        "",
                        ""
                    );   

                    $retorno['accion'] = 'Crear cuenta';
                    $retorno['msg'] = 'cuenta creada con exito';
                } else {
                    $retorno['errors'] = true;
                    $retorno['msj']    = "Error al intentar crear la cuenta";
                }
            } else {
                $retorno['accion'] = 'Crear usuario';
                $retorno['msg'] = 'Error. El correo ingresado ya esta en uso';
            }
        } 
        return Response::json($retorno);
    }

    public function ReadUser(Request $request) {  
        if ($request->has('id')) {
            $user = User::where(
                'id',
                $request->id
            )->with('propiedad')->first();

            $ub = UbicacionProp::where(
                'prop_id',
                $user->propiedad[0]->id
            )->first();
            
            $user->propiedad[0]->ubicacion = $ub;
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

    public function UpdateCuenta(Request $request) {  
        if ($request->has('id')) {
            $user = User::where(
                'id',
                $request->id
            )->first();

            if (!is_null($user)) {
                $user->name = $request->name;
                $user->email = $request->email;
                $user->phone = $request->phone;
                $user->save();

                $data['errors'] = false;
                $data['msg']    = 'Registro actualizado con exito';
            } else {
                $data['errors'] = true;
                $data['msg']    = 'Registro no encontrado';
            }
        } else {
            $data['errors'] = true;
            $data['msg']    = 'Datos requeridos';
        }
        return Response::json($data);
    }

    public function getUsers(Request $request) {  
        if ($request->has('id')) {
            $user = User::where(
                'id',
                $request->id
            )->with('propiedad')->first();

            $ub = UbicacionProp::where(
                'prop_id',
                $user->propiedad[0]->id
            )->first();
            
            $user->propiedad[0]->ubicacion = $ub;
            if (count($user) != 0) {
                $data['errors'] = false;
                $data['msg']    = $user;
            } else {
                $data['errors'] = true;
                $data['msg']    = 'Usuario no encontrado';
            }
            return Response::json($data);
        } else {
            $data["cant"] = User::count();
            if (!$request->has('rango') && $request->has('todos')) {
                $data["cuentas"] = User::all();
            } elseif (!$request->has('rango')) {
                $rango = 30;
                $data["cuentas"] = User::whereBetween(
                    'id', [
                        ($rango * 30), 
                        ($rango * 30) + 30
                    ]
                )->get();
            } 
        }
        return Response::json($data);
    }

    public function UpdateUser(Request $request)  {
        $rules = array(
            'nombre'                => '',
            'direccion'             => '',
            'ciudad'                => '',
            'numero_habitaciones'   => 'numeric',
            'tipo_propiedad_id'     => 'numeric',
            'estado_cuenta_id'      => 'numeric',
            'name'                  => '',
            'email'                 => 'email',
            'phone'                 => '',
            'rol_id'                => 'numeric',
            'estado_id'             => 'numeric',
            'latitud'               => '',
            'longitud'              => '',
        );

        $validator = Validator::make($request->all(), $rules);
        $data['accion'] = 'Actualizar Registro';

        if (!$validator->fails()) {

            $usuario = User::find($request->id);
            $propiedad_id = $usuario->propiedad[0]['id'];

            $usuario->update($request->all());
            $propiedad = Propiedad::find($propiedad_id);
            $propiedad->update($request->all());  

            $ubicacion = UbicacionProp::where('prop_id', $propiedad->id)->first(); 

            if (is_null($ubicacion)) {
                $ubicacion           = new UbicacionProp();
                $ubicacion->prop_id  = $propiedad_id;
                $ubicacion->location = new Point(
                    $request->longitud,
                    $request->latitud 
                );
                $ubicacion->save();

            } else {
                $location = new Point(
                    $request->longitud,
                    $request->latitud
                );
                $ubicacion->update(array('location' => $location));
            }

            $data['msg'] = 'Registro Actualizado Satisfactoriamente';
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
            try {
                $prop->ub = $prop->ubicacion;
            } catch (QueryException $e) {
                $prop->ub = null;
            }
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

        if ($request->has('new')) {
            return Response::json($propiedades);
        } else {
            return View('administrador.prop')->with(
                'props', 
                $propiedades
            );
        }
    }
}