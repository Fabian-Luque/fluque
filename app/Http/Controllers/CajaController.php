<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\ZonaHoraria;
use App\Caja;
use JWTAuth;
use \Carbon\Carbon;
use Response;
use Validator;



class CajaController extends Controller
{
    
	public function abrirCaja(Request $request)
	{
		$rules = array(

            'monto'   => 'numeric',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            $data = [
                'errors' => true,
                'msg'    => $validator->messages(),
            ];

            return Response::json($data, 400);

        } else {

		$user 	   		 = JWTAuth::parseToken()->toUser();
		$propiedad 	     = $user->propiedad[0];
		$enUTC 	   		 = Carbon::now();
		$zona_horaria_id = $propiedad->zona_horaria_id;
	    $zona_horaria    = ZonaHoraria::where('id', $zona_horaria_id)->first();
        $pais            = $zona_horaria->nombre;
	    $fecha_actual    = $enUTC->tz($pais);

	    $fecha_apertura  = $fecha_actual->format('Y-m-d');
	    $hora_apertura   = $fecha_actual->format('H:i:s');
	    $monto_apertura  = $request->input('monto');
	    
	    




       





        $data = [
            'errors' => false,
            'msg'    => 'Propiedad actualizada satisfactoriamente',
        ];

        return Response::json($data, 201);

        }





	}




}
