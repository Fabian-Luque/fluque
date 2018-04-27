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
use App\Caja;
use App\Reserva;
use App\Mensajeria; 
use App\Cliente; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Response;
use JWTAuth;

class UserController extends Controller {
    public function read(Request $request){
        return Response::json(User::all());
    }
    public function show($id){
        try { 
            $users        = User::where('id', $id)->with('propiedad.tipoPropiedad','propiedad.pais','propiedad.region','propiedad.zonaHoraria' ,'propiedad.tipoMonedas', 'propiedad.tipoCobro', 'propiedad.ubicacion', 'propiedad.PagoOnline')->with('rol.permisos')->get();
            $propiedad_id = $users[0]->propiedad[0]['id'];

            $clientes = [];

            $reservas = Reserva::whereHas('tipoHabitacion', function ($query) use ($propiedad_id) {
            $query->where('propiedad_id', $propiedad_id);})
            ->where('habitacion_id', null)
            ->whereIn('estado_reserva_id', [1,2,3,4,5])
            ->with('cliente')
            ->get();

            foreach ($reservas as $reserva) {
                if (!in_array($reserva->cliente, $clientes)) {
                    array_push($clientes, $reserva->cliente);
                }
            }

            foreach ($users as $user) {
                foreach ($user['propiedad'] as $propiedad) {
                    $caja_abierta    = Caja::where('propiedad_id', $propiedad->id)->where('estado_caja_id', 1)->first();
                    if (!is_null($caja_abierta)) {
                        $propiedad->caja_abierta = 1;
                    } else {
                        $propiedad->caja_abierta = 0;
                    }
                    $propiedad->reservas_motor    = count($clientes);
                }
            }

            $conv_no_leidas = Mensajeria::where(
                'receptor_id',
                $propiedad_id
            )->where(
                'estado',
                0
            )->get();

            $users[0]->msj_no = $conv_no_leidas->count();

            return $users;
            
        } catch (ModelNotFoundException $e) {
            $data = [
                'errors' => true,
                'msg'    => $e->getMessage(),
            ];
            return Response::json($data, 404);
        }
    }

    public function store(Request $request) {
        $usuario = new User();
        $validator = Validator::make(
            $request->all(), 
            $usuario->getRules()
        );

        if ($validator->fails()) {
            $data['errors'] = true;
            $data['msg'] = $validator->errors();
        } else {

            $codigo = str_random(50);
            $prop   = Propiedad::where('codigo', $codigo)->first();

            if (is_null($prop)) {
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
                $propiedad->codigo              = $codigo;

                $propiedad->save();
                $usuario->propiedad()->attach($propiedad->id);

                $data = [
                    'errors' => false,
                    'msg'    => 'usuario creado satisfactoriamente',
                ];
                return Response::json($data, 201);
            } else {
                $retorno = array(
                    'msj'    => "No se pudo crear cuenta",
                    'errors' => true,);
                return Response::json($retorno, 400);
            }
        }
    }

    public function crearUsuario(Request $request) {
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

    public function index(Request $request) {
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

        return $usuarios = User::whereHas(
            'propiedad', 
            function($query) use($propiedad_id) {
                $query->where(
                    'propiedades.id', 
                    $propiedad_id
                );
            }
        )->with('rol')->with('estado')->get();
    }

    public function update(Request $request, $id) {
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
            return Response::json($data);
        } else {
            $user = User::findOrFail($id);
            $user->update($request->all());
            $user->touch();

            $data = [
                'errors1' => false,
                'msg'    => 'Usuario actualizado satisfactoriamente',
            ];
            return Response::json($data);
        }
    }

    public function delete(Request $request) {
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
            $data['msg']    = trans('requests.success.code');
        }
        return Response::json($data);
    }

    public function getEstados() {
        $estados = Estado::all();
        return $estados;
    }
}
