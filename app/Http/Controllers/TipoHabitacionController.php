<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\TipoHabitacion;
use App\PrecioTemporada;
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

            $propiedad_id           = $request->input('propiedad_id');
            $propiedad              = Propiedad::where('id', $propiedad_id)->first();
            $moneda_propiedad       = $propiedad->tipoMonedas;
            $temporadas_propiedad   = $propiedad->temporadas;


            if ($propiedad->tipo_cobro_id != 3) {
                foreach ($temporadas_propiedad as $temporada) {
                    foreach ($moneda_propiedad as $moneda) {
                        $precio_temporada                     = new PrecioTemporada;

                        $precio_temporada->cantidad_huespedes = 1;
                        $precio_temporada->precio             = 0;
                        $precio_temporada->tipo_habitacion_id = $tipoHabitacion->id;
                        $precio_temporada->tipo_moneda_id     = $moneda->id;
                        $precio_temporada->temporada_id       = $temporada->id;;
                        $precio_temporada->save();
                    }
                }
            }else{

                $capacidad = $tipoHabitacion->capacidad;

                foreach ($temporadas_propiedad as $temporada) {
                    foreach ($moneda_propiedad as $moneda) {

                        for ($i=1; $i <= $capacidad  ; $i++) {
                            $precio_temporada                     = new PrecioTemporada;

                            $precio_temporada->cantidad_huespedes = $i;
                            $precio_temporada->precio             = 0;
                            $precio_temporada->tipo_habitacion_id = $tipoHabitacion->id;
                            $precio_temporada->tipo_moneda_id     = $moneda->id;
                            $precio_temporada->temporada_id       = $temporada->id;;
                            $precio_temporada->save();   
                        }
                    }
                }
            }

            $data = [
                'errors' => false,
                'msg'    => 'Tipo Habitacion creado satisfactoriamente',
            ];
            return Response::json($data, 201);

        }

	}

    public function editarPrecio(Request $request, $id)
    {
        $validator = Validator::make($request->all(),
            [
                'precio' => 'numeric',
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
                $precio = PrecioTemporada::findOrFail($id);
                $precio->update($request->all());
                $precio->touch();
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
                'msg'    => 'Precio actualizado satisfactoriamente',
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
