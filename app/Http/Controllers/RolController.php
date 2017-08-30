<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Rol;
use App\Permiso;
use App\Propiedad;
use App\Seccion;
use Response;
use Validator;

class RolController extends Controller
{
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

	    $roles = Rol::where('propiedad_id', $propiedad_id)
	    ->with(['permisos' => function ($q){
        	$q->select('permiso_id as id', 'nombre', 'estado');}])
	    ->get();
	}

	public function getSecciones(Request $request){

		$secciones = Seccion::with(['permisos' => function ($q){
        	$q->select('id', 'nombre', 'seccion_id');}])->get();
		
		foreach ($secciones as $seccion) {
			foreach ($seccion->permisos as $permiso) {
				$permiso->estado = 0;
			}
		}

		return $secciones;

	}


    public function store(Request $request)
	{
		$rules = array(
            'nombre'       	=> 'required',
            'permisos'      => 'array|required',
            'propiedad_id'	=> 'required|numeric',
        );

        $validator = Validator::make($request->all(), $rules);

     	if ($validator->fails()) {

            $data = [
                'errors' => true,
                'msg'    => $validator->messages(),
            ];

            return Response::json($data, 400);

        } else {

        	$rol  			    = new Rol();
        	$rol->nombre        = $request->get('nombre');
        	$rol->propiedad_id  = $request->get('propiedad_id');
        	$rol->save(); 

        	foreach ($request->get('permisos') as $permiso) {
        		$permiso_id = $permiso['id'];
        		$estado  	= $permiso['estado'];
        		$rol->permisos()->attach($permiso_id,['estado' => $estado]);
        	}

	        $data = [
	            'errors' => false,
	            'msg'    => 'Rol creado satisfactoriamente',
	        ];
	        return Response::json($data, 201);

	        }

	}

	public function update(Request $request, $id)
	{
        $rules = array(
            'nombre'            => '',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            $data = [
                'errors' => true,
                'msg'    => $validator->messages(),
            ];

            return Response::json($data, 400);

        } else {

            $rol = Rol::findOrFail($id);
            $rol->update($request->all());
            $rol->touch();

            $data = [
                'errors' => false,
                'msg'    => 'Rol actualizado satisfactoriamente',
            ];

            return Response::json($data, 201);

        }
	}

	public function destroy($id)
    {
        $rol = Rol::findOrFail($id);
        $rol->delete();

        $data = [
            'errors' => false,
            'msg'    => 'Rol eliminado satisfactoriamente',
        ];
        return Response::json($data, 202);

    }


}
