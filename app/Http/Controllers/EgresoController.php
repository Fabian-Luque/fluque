<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Egreso;
use App\Propiedad;
use App\Caja;
use App\EgresoCaja;
use App\EgresoPropiedad;
use JWTAuth;
use Response;
use Validator;


class EgresoController extends Controller
{
    public function index(Request $request)
    {
        if($request->has('propiedad_id')){
            $propiedad_id = $request->input('propiedad_id');
            $propiedad    = Propiedad::where('id', $propiedad_id)->first();
            if (is_null($propiedad)) {
                $retorno  = array(
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

        $egresos = Egreso::where('propiedad_id', $propiedad_id)->get();
        return $egresos;
    }

    public function store(Request $request)
	{
		$rules = array(
            'nombre'       	=> 'required',
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
        	$egreso  			= new Egreso();
        	$egreso->nombre        = $request->get('nombre');
        	$egreso->propiedad_id  = $request->get('propiedad_id');
        	$egreso->save(); 

	        $data = [
	            'errors' => false,
	            'msg'    => 'Egreso creado satisfactoriamente',];
	        return Response::json($data, 201);

  		}
	}

	public function update(Request $request, $id)
	{
        $rules = array(
            'nombre'     => '',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            $data = [
                'errors' => true,
                'msg'    => $validator->messages(),
            ];

            return Response::json($data, 400);

        } else {

            $egreso = Egreso::findOrFail($id);
            $egreso->update($request->all());
            $egreso->touch();

            $data = [
                'errors' => false,
                'msg'    => 'Egreso actualizado satisfactoriamente',];
            return Response::json($data, 201);

        }
	}

	public function ingresarEgresoCaja(Request $request)
    {
        if ($request->has('propiedad_id')) {
            $propiedad_id = $request->input('propiedad_id');
            $propiedad    = Propiedad::where('id', $propiedad_id)->with('tipoMonedas')->first();
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

        if ($request->has('egreso_id')) {
            $egreso_id = $request->input('egreso_id');
            $egreso = Egreso::where('id', $egreso_id)->first();
        } else {
            $retorno = array(
                'msj'    => "No se envia egreso_id",
                'errors' => true);
            return Response::json($retorno, 400);
        }

    	if ($request->has('monto') && $request->has('descripcion') && $request->has('tipo_moneda_id')) {
    		$monto 			= $request->get('monto');
			$descripcion 	= $request->get('descripcion');
			$tipo_moneda_id = $request->get('tipo_moneda_id');
    	} else {
	        $retorno = array(
                'msj'    => "Solicitud incompleta",
                'errors' => true);
            return Response::json($retorno, 400);
    	}
        
        $caja_abierta  = Caja::where('propiedad_id', $propiedad_id)->where('estado_caja_id', 1)->first();

        if (!is_null($caja_abierta)) {

    		$user = JWTAuth::parseToken()->toUser();

    		$egreso 		 		= new EgresoCaja();
    		$egreso->monto  		= $monto;
    		$egreso->descripcion    = $descripcion;
    		$egreso->egreso_id 		= $egreso_id;
    		$egreso->caja_id    	= $caja_abierta->id;
    		$egreso->user_id    	= $user->id;
    		$egreso->tipo_moneda_id = $tipo_moneda_id;
    		$egreso->save();

    		$data = array(
                'errors' => false,
                'msg'    => 'Egreso ingresado satisfactoriamente',);
            return Response::json($data, 201);
    		
        } else {
            $retorno = array(
                'msj'    => "No hay caja abierta",
                'errors' => true);
            return Response::json($retorno, 400);
        }

    }

	public function ingresarEgresoPropiedad(Request $request)
    {
        if ($request->has('propiedad_id')) {
            $propiedad_id = $request->input('propiedad_id');
            $propiedad    = Propiedad::where('id', $propiedad_id)->with('tipoMonedas')->first();
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

        if ($request->has('egreso_id')) {
            $egreso_id = $request->input('egreso_id');
            $egreso = Egreso::where('id', $egreso_id)->first();
        } else {
            $retorno = array(
                'msj'    => "No se envia egreso_id",
                'errors' => true);
            return Response::json($retorno, 400);
        }

    	if ($request->has('monto') && $request->has('descripcion') && $request->has('tipo_moneda_id')) {
    		$monto 			= $request->get('monto');
			$descripcion 	= $request->get('descripcion');
			$tipo_moneda_id = $request->get('tipo_moneda_id');
    	} else {
	        $retorno = array(
                'msj'    => "Solicitud incompleta",
                'errors' => true);
            return Response::json($retorno, 400);
    	}

		$user = JWTAuth::parseToken()->toUser();

		$egreso 		 		= new EgresoPropiedad();
		$egreso->monto  		= $monto;
		$egreso->descripcion    = $descripcion;
		$egreso->egreso_id 		= $egreso_id;
		$egreso->propiedad_id   = $propiedad_id;
		$egreso->user_id    	= $user->id;
		$egreso->tipo_moneda_id = $tipo_moneda_id;
		$egreso->save();

		$data = array(
            'errors' => false,
            'msg'    => 'Egreso ingresado satisfactoriamente',);
        return Response::json($data, 201);

    }




}
