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

class UserDashController extends Controller {

	public function CreateUser(Request $request) {
        if ($request->has('name') && $request->has('email') && $request->has('password') && $request->has('phone') && $request->has('nombre') && $request->has('direccion') && $request->has('tipo_propiedad_id') && $request->has('tipo_cuenta')) {
            $us = User::where('email',$request->email)->first();
            if (!isset($us->email)) {
                $usuario = new User();
                $usuario->name                 = $request->name;
                $usuario->email                = $request->email;
                $usuario->password             = $request->password;
                $usuario->phone                = $request->phone;
                $usuario->save();

                $propiedad                      = new Propiedad();
                $propiedad->id                  = $usuario->id;
                $propiedad->nombre              = $request->nombre;
                $propiedad->direccion           = $request->direccion;
                $propiedad->tipo_propiedad_id   = $request->tipo_propiedad_id;
                $propiedad->save();

                $tipocuenta                     = new Estadocuenta();
                $tipocuenta->propiedad_id       = $propiedad->id;
                $tipocuenta->estado             = $request->tipo_cuenta;
                $tipocuenta->save();
                
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
        if ($request->has('id') && $request->has('name') && $request->has('email') && $request->has('password') && $request->has('phone') && $request->has('nombre') && $request->has('direccion') && $request->has('tipo_propiedad_id') && $request->has('tipo_cuenta')) {
            $usuario = User::find($request->id);
            if (!isset($us->email)) {
                $usuario->name                 = $request->name;
                $usuario->email                = $request->email;
                $usuario->password             = $request->password;
                $usuario->phone                = $request->phone;
                $usuario->save();

                $propiedad                      = Propiedad::where('user_id',$usuario->id)->first();
                $propiedad->nombre              = $request->nombre;
                $propiedad->direccion           = $request->direccion;
                $propiedad->tipo_propiedad_id   = $request->tipo_propiedad_id;
                $propiedad->save();

                $tipocuenta                     = Estadocuenta::where('propiedad_id',$propiedad->id)->first();
                $tipocuenta->propiedad_id       = $propiedad->id;
                $tipocuenta->estado             = $request->tipo_cuenta;
                $tipocuenta->save();
                
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
        return View('administrador.prop')->with(
            'props', 
            $propiedades
        );
    }
}