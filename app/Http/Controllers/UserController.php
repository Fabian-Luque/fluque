<?php

namespace App\Http\Controllers;

use App\Habitacion;
use App\Http\Controllers\Controller;
use App\Propiedad;
use App\Servicio;
use App\TipoHabitacion;
use App\User;
use App\ZonaHoraria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Response;
use JWTAuth;


class UserController extends Controller {

    public function show(Request $request) {  
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

    public function store(Request $request) {
        $rules = array(
            'name'                => 'required',
            'email'               => 'required|unique:users,email',
            'phone'               => 'required',
            'password'            => 'required|min:6',
            'nombre'              => 'required',
            'tipo_propiedad_id'   => 'required|numeric',
            'numero_habitaciones' => 'required|numeric',
            'ciudad'              => 'required',
            'direccion'           => 'required',

        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        } else {
            $usuario                       = new User();
            $usuario->name                 = $request->get('name');
            $usuario->email                = $request->get('email');
            $usuario->password             = $request->get('password');
            $usuario->phone                = $request->get('phone');
            $usuario->save();

            $propiedad                      = new Propiedad();
            $propiedad->id                  = $usuario->id;
            $propiedad->nombre              = $request->get('nombre');
            $propiedad->numero_habitaciones = $request->get('numero_habitaciones');
            $propiedad->ciudad              = $request->get('ciudad');
            $propiedad->direccion           = $request->get('direccion');
            $propiedad->tipo_propiedad_id   = $request->get('tipo_propiedad_id');
            $propiedad->user_id             = $usuario->id;
            $propiedad->save();

            $data = [
                'errors' => false,
                'msg'    => 'usuario creado satisfactoriamente',
            ];

            return Response::json($data, 201);
        }
    }



    public function update(Request $request, $id) {
        $rules = array(
            'name'     => '',
            'email'    => 'email',
            'password' => 'min:6',
            'phone'    => '',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $data = [
                'errors' => true,
                'msg'    => $validator->messages(),
            ];
            return Response::json($data, 400);
        } else {
            $user = User::findOrFail($id);
            $user->update($request->all());
            $user->touch();

            $data = [
                'errors1' => false,
                'msg'    => 'Usuario actualizado satisfactoriamente',
            ];
            return Response::json($data, 201);
        }
    }

    public function prueba(Request $request) {
            $data = [
                'errors' => $request->id,
                'msg'    => 'benjaaaaaaaaaaaaa!!!!!!',
            ];
        return Response::json($data);
    }

    public function delete(Request $request) {
        dd($request->id);
        if ($request->has('id')) {
        if ($user = User::find($id)) {
            $user->delete();

            $data['errors'] = false;
            $data['msg']    = 'Usuario eliminado satisfactoriamente';
        } else {
            $data['errors'] = true;
            $data['msg']    = 'Usuario no encontrado';
        }
    } else {
        $data['errors'] = true;
        $data['msg']    = 'datos requeridos';
    }
        return Response::json($data, 200);
    
    }
}
