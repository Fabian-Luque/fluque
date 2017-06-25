<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\TipoHabitacion;
use App\Propiedad;
use Illuminate\Support\Facades\Validator;
use Response;

class TipoHabitacionController extends Controller
{
	public function index(Request $request)
	{
		if ($request->has('propiedad_id')) {
			$propiedad_id 	= $request->input('propiedad_id');
            $propiedad      = Propiedad::where('id', $propiedad_id)->first();
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

		$tipos_habitacion	= TipoHabitacion::where('propiedad_id', $propiedad_id)->get();

		return $tipos_habitacion;

	}   


	public function store(Request $request)
	{


		$rules = array(

            'nombre'       => 'required',
            'capacidad'    => 'required|numeric',
            'propiedad_id' => 'required|numeric',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            $data = [

                'errors' => true,
                'msg'    => $validator->messages(),

            ];

            return Response::json($data, 400);

        } else {

            $tipoHabitacion = TipoHabitacion::create($request->all());

            $data = [
                'errors' => false,
                'msg'    => 'Tipo Habitacion creado satisfactoriamente',
            ];
            return Response::json($data, 201);

        }

	}

	public function update(Request $request ,$id)
	{

		$validator = Validator::make($request->all(),
            [
                'nombre'	 => '',
                'capacidad'  => '',
            ]
        );

        if ($validator->fails()) {
            $data = [
                'errors' => true,
                'msg'    => $validator->messages(),
            ];
            return Response::json($data, 400);
        } else {
            try {
                $tipoHabitacion = TipoHabitacion::findOrFail($id);
                $tipoHabitacion->update($request->all());
                $tipoHabitacion->touch();
            } catch (QueryException $e) {
                $data = [
                    'errors' => true,
                    'msg'    => $e->message(),
                ];
                return Response::json($data, 400);
            } catch (ModelNotFoundException $e) {
                $data = [
                    'errors' => true,
                    'msg'    => $e->getMessage(),
                ];
                return Response::json($data, 404);
            }
            $data = [
                'errors' => false,
                'msg'    => 'Tipo Habitacion actualizado satisfactoriamente',
            ];
            return Response::json($data, 201);
        }

	}

	public function destroy($id)
    {

        $tipoHabitacion = TipoHabitacion::findOrFail($id);
        $tipoHabitacion->delete();

        $data = [

            'errors' => false,
            'msg'    => 'Tipo Habitacion eliminado satisfactoriamente',

        ];

        return Response::json($data, 202);

    }



}
