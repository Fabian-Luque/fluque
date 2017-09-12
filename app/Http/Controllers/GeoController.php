<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Response;
use App\UbicacionProp;   
use Grimzy\LaravelMysqlSpatial\Types\Point;

class GeoController extends Controller {

    public function UbicacionSave(Request $request) {

    	if ($request->has('id') && $request->has('latitud') && $request->has('longitud')) {
    		$ubp = new UbicacionProp();
			$ubp->prop_id = $request->id;
			$ubp->location = new Point(
				$request->latitud, 
				$request->longitud
			);
			$ubp->save();

			$data['errors'] = false;
            $data['msg']    = 'Localizacion registrada con exito';
            $data['ubprop']	= $ubp;
    	} else {
    		$data['errors'] = true;
            $data['msg']    = 'Datos requeridos';
    	}
		return Response::json($ubp);
    }

    public function UbicacionRead(Request $request) {
    	# code...
    }

    public function UbicacionUpdate(Request $request) {
    	# code...
    }

    public function UbicacionDelete(Request $request) {
    	# code...
    }

}
