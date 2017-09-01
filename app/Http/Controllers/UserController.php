<?php

namespace App\Http\Controllers;

use App\Habitacion;
use App\Http\Controllers\Controller;
use App\Propiedad;
use App\Servicio;
use App\TipoHabitacion;
use App\User;
use App\ZonaHoraria;
use App\Estado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Response;
use JWTAuth;

class UserController extends Controller
{

    public function show($id)
    {

        try {

            $users = User::where('id', $id)->with('propiedad.tipoMonedas.clasificacionMonedas')->with('rol.permisos')->get();

            return $users;

        } catch (ModelNotFoundException $e) {
            $data = [
                'errors' => true,
                'msg'    => $e->getMessage(),
            ];
            return Response::json($data, 404);
        }

    }

    public function store(Request $request)
    {

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
            $usuario->rol_id               = 1;
            $usuario->estado_id            = 1;

            $usuario->save();

            $propiedad                      = new Propiedad();
            $propiedad->nombre              = $request->get('nombre');
            $propiedad->numero_habitaciones = $request->get('numero_habitaciones');
            $propiedad->ciudad              = $request->get('ciudad');
            $propiedad->direccion           = $request->get('direccion');
            $propiedad->tipo_propiedad_id   = $request->get('tipo_propiedad_id');

            $propiedad->save();

            $usuario->propiedad()->attach($propiedad->id);

            $data = [
                'errors' => false,
                'msg'    => 'usuario creado satisfactoriamente',

            ];

            return Response::json($data, 201);

        }

    }

    public function crearUsuario(Request $request)
    {
        $rules = array(
            'name'                => 'required',
            'email'               => 'required|unique:users,email',
            'phone'               => 'required',
            'password'            => 'required|min:6',
            'rol_id'              => 'required|numeric',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator->errors());

        } else {

            $user = JWTAuth::parseToken()->toUser();

            $propiedad = $user->propiedad;
            foreach ($propiedad as $prop) {
                $propiedad_id = $prop->id;
            }

            $usuario                       = new User();
            $usuario->name                 = $request->get('name');
            $usuario->email                = $request->get('email');
            $usuario->password             = $request->get('password');
            $usuario->phone                = $request->get('phone');
            $usuario->rol_id               = $request->get('rol_id');
            $usuario->estado_id            = 1;
            $usuario->save();

            $usuario->propiedad()->attach($propiedad_id);

            $data = [
                'errors' => false,
                'msg'    => 'usuario creado satisfactoriamente',

            ];

            return Response::json($data, 201);

        }
    }

    public function index(Request $request)
    {
        if ($request->has('propiedad_id')) {
            $propiedad_id = $request->input('propiedad_id');
            $propiedad    = Propiedad::where('id', $propiedad_id)->first();
            if (is_null($propiedad)) {
                $retorno = array(
                    'msj'    => "Propiedad no encontrada",
                    'errors' => true);
                return Response::json($retorno, 404);
            }
        } else {
            $retorno = array(
                'msj'    => "No se envia propiedad_id",
                'errors' => true);
            return Response::json($retorno, 400);
        }

        return $usuarios = User::whereHas('propiedad', function($query) use($propiedad_id){
                $query->where('propiedades.id', $propiedad_id);
        })->with('rol')->with('estado')->get();

    }

    public function update(Request $request, $id)
    {

        $rules = array(

            'name'     => '',
            'email'    => 'email',
            'password' => 'min:6',
            'phone'    => '',
            'rol_id'   => 'numeric',
            'estado_id'=> 'numeric',

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

                'errors' => false,
                'msg'    => 'Usuario actualizado satisfactoriamente',

            ];

            return Response::json($data, 201);

        }

    }

    public function getEstados()
    {
        $estados = Estado::all();

        return $estados;

    }

}
