<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Response;
use App\UbicacionProp;   
use Grimzy\LaravelMysqlSpatial\Types\Point;
use GeneaLabs\Phpgmaps\Phpgmaps;
use App\clases\DiseñoMapa;
use Illuminate\Support\Facades\Validator;

class GeoController extends Controller {

    public function GoogleMapsDir(Request $request) {   
        $diseño = new DiseñoMapa();
        $config['center'] = 'auto';
        $config['apiKey'] = 'AIzaSyCjpd08Tu7zozwrj3-Sb3RIBUv13gnY3SQ';
        $config['zoom'] = '15';
        $config['styles'] = $diseño->getDis(); 
        $config['places'] = true;
        $config['placesAutocompleteInputID'] = 'direccion';
        $config['placesAutocompleteBoundsMap'] = true; 
        $config['placesAutocompleteOnChange'] = ''; // colocar nombre funcion
        $config['directionsStart'] = '';
        $config['directionsEnd'] = '';
        $config['disableDefaultUI'] = true;
        $config['onboundschanged'] = '
            if (!centreGot) {
                var mapCentre = map.getCenter();
                marker_0.setOptions({
                    position: new google.maps.LatLng(
                        mapCentre.lat(), 
                        mapCentre.lng()
                    ) 
                });
            }
            centreGot = true;';
        $pgm = new Phpgmaps();
        $pgm->initialize($config);
        $hoteles = UbicacionProp::all();

        $marker = array();
        $marker['onclick'] = '';
        $marker['infowindow_content'] = 'Mi Ubicacion';
        $marker['icon'] = url('assets/img/marker_miposision.png');
        $pgm->add_marker($marker);

        if ($hoteles->count() != 0) {
            foreach ($hoteles as $hotel) {  
                if (isset($hotel->id)) {
                    $marker = array();
                    $marker['position'] = ''.$hotel->location->getLat().', '.$hotel->location->getLng().'';
                    $marker['onclick'] = 'mostrar();';
                    $marker['icon'] = url('assets/img/marker_hotel.png');
                    $pgm->add_marker($marker);
                }
            }
        }

        return view('administrador.gmap')->with(
            'map', 
            $pgm->create_map()
        );
    }

	public function Gmaps(Request $request) {
		$diseño = new DiseñoMapa();
        $config['center'] = 'auto';
        $config['apiKey'] = 'AIzaSyCjpd08Tu7zozwrj3-Sb3RIBUv13gnY3SQ';
        $config['zoom'] = '15';
        $config['styles'] = $diseño->getDis(); 
        $config['places'] = true;
        $config['placesAutocompleteInputID'] = 'direccion';
        $config['placesAutocompleteBoundsMap'] = true; 
        $config['disableDefaultUI'] = true;
		$config['onboundschanged'] = '
			if (!centreGot) {
				var mapCentre = map.getCenter();
				marker_0.setOptions({
					position: new google.maps.LatLng(
						mapCentre.lat(), 
						mapCentre.lng()
					) 
				});
			}
			centreGot = true;';
        $pgm = new Phpgmaps();
        $pgm->initialize($config);
        $hoteles = UbicacionProp::all();

        $marker = array();
        $marker['onclick'] = '';
		$marker['infowindow_content'] = 'Mi Ubicacion';
		$marker['icon'] = url('assets/img/marker_miposision.png');
		$pgm->add_marker($marker);

        if ($hoteles->count() != 0) {
        	foreach ($hoteles as $hotel) {	
        		if (isset($hotel->id)) {
        			$marker = array();
        			$marker['position'] = ''.$hotel->location->getLat().', '.$hotel->location->getLng().'';
					$marker['onclick'] = 'mostrar();';
					$marker['icon'] = url('assets/img/marker_hotel.png');
					$pgm->add_marker($marker);
        		}
        	}
    	}

        return view('administrador.gmap')->with(
        	'map', 
        	$pgm->create_map()
        );
	}

    public function GoogleMaps(Request $request) {
        $diseño = new DiseñoMapa();
        $config['center'] = 'auto';
        $config['apiKey'] = 'AIzaSyCjpd08Tu7zozwrj3-Sb3RIBUv13gnY3SQ';
        $config['zoom'] = '15';
        $config['styles'] = $diseño->getDis(); 
        $config['places'] = true;
        $config['placesAutocompleteInputID'] = 'direccion';
        $config['placesAutocompleteBoundsMap'] = true; 
        $config['disableDefaultUI'] = true;
        $config['onboundschanged'] = '
            if (!centreGot) {
                var mapCentre = map.getCenter();
                marker_0.setOptions({
                    position: new google.maps.LatLng(
                        mapCentre.lat(), 
                        mapCentre.lng()
                    ) 
                });
            }
            centreGot = true;';
        $pgm = new Phpgmaps();
        $pgm->initialize($config);
        $hoteles = UbicacionProp::all();

        $marker = array();
        $marker['onclick'] = '';
        $marker['infowindow_content'] = 'Mi Ubicacion';
        $marker['icon'] = url('assets/img/marker_miposision.png');
        $pgm->add_marker($marker);

        if ($hoteles->count() != 0) {
            foreach ($hoteles as $hotel) {  
                if (isset($hotel->id)) {
                    $marker = array();
                    $marker['position'] = ''.$hotel->location->getLat().', '.$hotel->location->getLng().'';
                    $marker['onclick'] = 'mostrar();';
                    $marker['icon'] = url('assets/img/marker_hotel.png');
                    $pgm->add_marker($marker);
                }
            }
        }
        return $pgm->create_map();
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

    public function AddLocatePropiedad(Request $request) {   
        $diseño = new DiseñoMapa();
        $config['center'] = 'auto';
        $config['apiKey'] = 'AIzaSyCjpd08Tu7zozwrj3-Sb3RIBUv13gnY3SQ';
        $config['zoom'] = '15';
        $config['styles'] = $diseño->getDis(); 
        $config['places'] = true;
        $config['placesAutocompleteInputID'] = 'direccion';
        $config['placesAutocompleteBoundsMap'] = true; 
        $config['placesAutocompleteOnChange'] = ''; // colocar nombre funcion
        $config['directionsStart'] = '';
        $config['directionsEnd'] = '';
        $config['disableDefaultUI'] = true;
        $config['onboundschanged'] = '
            if (!centreGot) {
                var mapCentre = map.getCenter();
                marker_0.setOptions({
                    position: new google.maps.LatLng(
                        mapCentre.lat(), 
                        mapCentre.lng()
                    ) 
                });
            }
            centreGot = true;';
        $pgm = new Phpgmaps();
        $pgm->initialize($config);
        $hoteles = UbicacionProp::all();

        $marker = array();
        $marker['onclick'] = '';
        $marker['draggable'] = true;
        $marker['infowindow_content'] = 'Ubicacion de la propiedad';
        $marker['icon'] = url('assets/img/marker_miposision.png');
        $marker['ondragend'] = "Save_Locate_Prop(event.latLng.lat(), event.latLng.lng());";
        $pgm->add_marker($marker);

        if ($hoteles->count() != 0) {
            foreach ($hoteles as $hotel) {  
                if (isset($hotel->id)) {
                    $marker = array();
                    $marker['position'] = ''.$hotel->location->getLat().', '.$hotel->location->getLng().'';
                    $marker['onclick'] = 'mostrar();';
                    $marker['icon'] = url('assets/img/marker_hotel.png');
                    $pgm->add_marker($marker);
                }
            }
        }
        return Response::json($pgm->create_map());
    }

    public function PropiedadesCercanas(Request $request) {
        $validator = Validator::make(
            $request->all(), 
            array(
                'latitud'  => 'required',
                'longitud' => 'required',
                'radio'    => 'required',
            )
        );

        if ($validator->fails()) {
            $retorno['errors'] = true;
            $retorno["msj"] = $validator->errors();
        } else {
            try {
                $u_props = new UbicacionProp();
                $propiedades = $u_props->getPropiedadesCercanas(
                    $request->latitud,
                    $request->longitud,
                    $request->radio
                );

                $retorno['errors'] = false;
                $retorno['msj']    = $propiedades;
            } catch (\Illuminate\Database\QueryException $e) {
                $retorno['errors'] = true;
                $retorno['msj']    = "No existen propiedades en un radio ".$request->radio." KM^2";
            }
        }
        return Response::json($retorno);
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