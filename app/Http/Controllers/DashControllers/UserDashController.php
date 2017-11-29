<?php

namespace App\Http\Controllers\DashControllers;

use App\Estadocuenta;
use App\Events\ReservasMotorEvent;
use App\Http\Controllers\Controller;
use App\Propiedad;
use App\TipoPropiedad;
use App\User;
use App\QvoUser;
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


class UserDashController extends Controller {

    public function CreateUser(Request $request) {
        if ($request->has('name') && $request->has('email') && $request->has('password') && $request->has('phone') && $request->has('nombre') && $request->has('direccion') && $request->has('tipo_propiedad_id') && $request->has('tipo_cuenta') && $request->has('ciudad') && $request->has('numero_habitaciones') && $request->has('latitud') && $request->has('longitud')) {
            $us = User::where('email',$request->email)->first();
            if (!isset($us->email)) {
                $usuario = new User();
                $usuario->name                  = $request->name;
                $usuario->email                 = $request->email;
                $usuario->password              = $request->password;
                $usuario->phone                 = $request->phone;
                $usuario->rol_id                = 1;
                $usuario->estado_id             = 1;
                $usuario->save();

                $propiedad                      = new Propiedad();
                $propiedad->id                  = $usuario->id;
                $propiedad->nombre              = $request->nombre;
                $propiedad->direccion           = $request->direccion;
                $propiedad->ciudad              = $request->ciudad;
                $propiedad->numero_habitaciones = $request->numero_habitaciones;
                $propiedad->tipo_propiedad_id   = $request->tipo_propiedad_id;
                $propiedad->estado_cuenta_id    = $request->tipo_cuenta;
                $propiedad->save();

                $usuario->propiedad()->attach($propiedad->id);

                $ubicacion           = new UbicacionProp();
                $ubicacion->prop_id  = $propiedad->id;
                $ubicacion->location = new Point(
                    $request->latitud, 
                    $request->longitud
                );
                $ubicacion->save();

                $job = new ProcesoQVO($usuario);
                dispatch($job);
                
                $data['accion'] = 'Crear usuario';
                $data['msg'] = 'Usuario creado satisfactoriamente';
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

            if (isset($prop)) {
                $prop->tipo_propiedad = TipoPropiedad::find(
                    $prop->tipo_propiedad_id
                )->nombre;
            }
            if (isset($prop->estado_cuenta_id)) {
                $prop->estado_cuenta = Estadocuenta::find(
                    $prop->estado_cuenta_id
                )->nombre;
            }
        }
        return View('administrador.prop')->with(
            'props', 
            $propiedades
        );
    }
}