<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Response;
use App\UbicacionProp;   

class GeoController extends Controller {

    public function UbicacionSave(Request $request) {
    	$ubp = new UbicacionProp();
		$ubp->prop_id = 4;
		$ubp->location = new Point(
			40.7484404, 
			-73.9878441
		);
		$ubp->save();

		return Response::json($ubp);
    }
}
