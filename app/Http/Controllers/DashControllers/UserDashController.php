<?php

namespace App\Http\Controllers\DashControllers;

use App\Habitacion;
use App\Http\Controllers\Controller;
use App\Propiedad;
use App\Servicio;
use App\TipoHabitacion;
use App\TipoPropiedad;
use App\Estadocuenta;
use App\User;
use App\ZonaHoraria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Response;
use JWTAuth;
use Illuminate\Support\Facades\Event;
use App\Events\ReservasMotorEvent;

class UserDashController extends Controller {

	public function CreateUser(Request $request) {
        if ($request->has('name') && $request->has('email') && $request->has('password') && $request->has('phone') && $request->has('nombre') && $request->has('direccion') && $request->has('tipo_propiedad_id') && $request->has('tipo_cuenta') && $request->has('ciudad') && $request->has('numero_habitaciones')) {
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

                $data['accion'] = 'Crear usuario';
                $data['msg'] = 'Usuario creado satisfactoriamente';

                Event::fire(
                    new ReservasMotorEvent($usuario)
                );
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
        if ($request->has('id') && $request->has('name') && $request->has('email') && $request->has('phone') && $request->has('nombre') && $request->has('direccion') && $request->has('tipo_propiedad_id') && $request->has('tipo_cuenta')) {
            $usuario = User::find($request->id);
            if (!isset($us->email)) {
                $usuario->name                  = $request->name;
                $usuario->email                 = $request->email;
                $usuario->phone                 = $request->phone;
                $usuario->rol_id                = 1;
                $usuario->estado_id             = 1;
                $usuario->save();
                $usuario->propiedad()->detach($usuario->id);
                $propiedad                      = Propiedad::where(
                    'id',
                    $usuario->id
                )->first();
                
                $propiedad->nombre              = $request->nombre;
                $propiedad->direccion           = $request->direccion;
                $propiedad->ciudad              = $request->ciudad;
                $propiedad->numero_habitaciones = $request->numero_habitaciones;
                $propiedad->tipo_propiedad_id   = $request->tipo_propiedad_id;
                $propiedad->estado_cuenta_id    = $request->tipo_cuenta;
                $propiedad->save();

                $usuario->propiedad()->attach($propiedad->id);
                
                $data['accion'] = 'Actualizar usuario';
                $data['msg'] = 'Usuario Actualizado satisfactoriamente';
            } else {
                $data['accion'] = 'Actualizar usuario';
                $data['msg'] = 'Error. El correo ingresado ya esta en uso';
            }
        } else {
            $data['accion'] = 'Actualizar usuario';
            $data['msg'] = 'Datos requeridos';
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
        foreach ($propiedades as $prop) {
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

    public function evento(Request $request) {
        $usuario = User::find(1);

        Event::fire(
            new ReservasMotorEvent($usuario)
        );
    }
}