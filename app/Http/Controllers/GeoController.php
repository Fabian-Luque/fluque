<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Response;
use App\UbicacionProp;   
use Grimzy\LaravelMysqlSpatial\Types\Point;
use GeneaLabs\Phpgmaps\Phpgmaps;

class GeoController extends Controller {

	public function Gmaps(Request $request) {

	// $diseño = new DiseñoMapa();
        $config['center'] = 'auto';
        $config['apiKey'] = 'AIzaSyCjpd08Tu7zozwrj3-Sb3RIBUv13gnY3SQ';
        
        $config['zoom'] = '15';
    //    $config['styles'] = $diseño->getDis(); 
        $config['places'] = true;
        $config['placesAutocompleteInputID'] = 'direccion';
        $config['placesAutocompleteBoundsMap'] = true; 

        $pgm = new Phpgmaps();
        $pgm->initialize($config);
        $hoteles = UbicacionProp::all();

        if ($hoteles->count() != 0) {
        	foreach ($hoteles as $hotel) {	
        		if (isset($hotel->id)) {
        			$marker = array();
        			$marker['position'] = ''.$hotel->location->getLat().', '.$hotel->location->getLng().'';
					$marker['onclick'] = "
            			document.getElementById('light').style.display='block';
            			document.getElementById('fade').style.display='block';
        			";

					//$marker['infowindow_content'] = 'Mi Ubicacion';
					$marker['icon'] = url('assets/img/marker_hotel.png');
					$pgm->add_marker($marker);
        		}
        	}
    	}

		$marker = array();
		
		$marker['position'] = 'map.getCenter().lat(), map.getCenter().lng()';
		$marker['onclick'] = "
            document.getElementById('light').style.display='block';
            document.getElementById('fade').style.display='block';
        ";

		//$marker['infowindow_content'] = 'Mi Ubicacion';
		$marker['icon'] = url('assets/img/marker_miposision.png');
		$pgm->add_marker($marker);

        return view('administrador.gmap')->with('map', $pgm->create_map());
	}

    public function UbicacionCreate(Request $request) {
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
