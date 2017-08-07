<?php

namespace App\Http\Controllers\DashControllers;

use App\Habitacion;
use App\Http\Controllers\Controller;
use App\Propiedad;
use App\Servicio;
use App\TipoHabitacion;
use App\TipoPropiedad;
use App\User;
use App\ZonaHoraria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Response;
use JWTAuth;

class UserDashController extends Controller {

	public function CreateUser(Request $request) {
        if ($request->has('name') && $request->has('email') && $request->has('password') && $request->has('phone') && $request->has('nombre') && $request->has('direccion') && $request->has('tipo_propiedad_id')) {
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
                $propiedad->user_id             = $usuario->id;
                $propiedad->save();
                
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
        return redirect('dash/adminuser')->with('respuesta', $data);
	}

	public function ReadUser(Request $request) {  
        if ($request->has('id')) {
            $users = User::where(
                'id', 
                $request->id
            )->with('propiedad.tipoMonedas.clasificacionMonedas')
            ->get();

            if (count($users) != 0) {
                $status = 200;
                $data = $users;
            } else {
                $status = 200;
                $data['errors'] = false;
                $data['msg']    = 'Usuario no encontrado';
            }
            return Response::json($data, $status);
        } else {
            $status = 200;
            $data = User::all();
            return View('administrador.user')->with('users', $data);
        }
    }

	public function UpdateUser(Request $request)  {
        $validator = Validator::make(
        	$request->all(), 
        	array(
            	'name'     => '',
            	'email'    => 'email',
            	'password' => 'min:6',
            	'phone'    => '',
        	)
        );

        if ($validator->fails()) {
            $data['errors'] = true;
            $data['msg'] = $validator->messages();
            return Response::json($data);
        } else {
            $user = User::find($request->id);
            $user->update($request->all());
            $user->touch();

            $data = [
                'errors1' => false,
                'msg'    => 'Usuario actualizado satisfactoriamente',
            ];
            return Response::json($data);
        }
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
        return View('administrador.reguser')->with('tprops', $TipoPropiedad);
    }

    public function getViewPropiedad(Request $request) {
        $propiedades = TipoPropiedad::all();
        return View('administrador.prop')->with('tprops', $propiedades);
    }
}