<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Response;
use App\UbicacionProp;   
use App\Propiedad;
use App\Habitacion;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use GeneaLabs\Phpgmaps\Phpgmaps;
use App\clases\DiseñoMapa;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

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
                $request->longitud,
				$request->latitud 
			);
			$ubp->save();

			$data['errors'] = false;
            $data['msg']    = 'Localizacion registrada con exito';
            $data['ubprop']	= $ubp;
    	} else {
    		$data['errors'] = true;
            $data['msg']    = 'Datos requeridos';
    	}
		return Response::json($data);
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
                'latitud'      => 'required',
                'longitud'     => 'required',
                'radio'        => 'required',
                'fecha_inicio' => 'required',
                'fecha_fin'    => 'required',
                'prop_id'      => 'required',
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
                    $request->radio,
                    $request->prop_id
                );
                $propiedades = collect($propiedades);

                $fecha_inicio = $request->fecha_inicio;
                $fecha_fin    = $request->fecha_fin;

                foreach ($propiedades as $prop) {
                    $prop->propiedad  = Propiedad::find($prop->prop_id);
                    $property         = Propiedad::where('id', $prop->prop_id)->with('tiposHabitacion')->first();
                    $tipos_habitacion = $property->tiposHabitacion;
                    $config           = $tipos_habitacion->where('venta_propiedad', 0);

                    $habitaciones_disponibles = Habitacion::where(
                        'propiedad_id', 
                        $prop->prop_id
                    )->whereDoesntHave(
                        'reservas', 
                        function ($query) use ($fecha_inicio, $fecha_fin) {
                            $query->whereIn('estado_reserva_id', [1,2,3,4,5])->where(
                                function ($query) use ($fecha_inicio, $fecha_fin) {
                                    $query->where(
                                        function ($query) use ($fecha_inicio, $fecha_fin) {
                                            $query->where('checkin', '>=', $fecha_inicio);
                                            $query->where('checkin', '<',  $fecha_fin);
                                        }
                                    );
                                    $query->orWhere(
                                        function($query) use ($fecha_inicio,$fecha_fin){
                                            $query->where('checkin', '<=', $fecha_inicio);
                                            $query->where('checkout', '>',  $fecha_inicio);
                                        }
                                    );                
                                }
                            );
                        }
                    )->get();

                    $prop->n_habitaciones_disponibles = $habitaciones_disponibles->count();

                    if ($habitaciones_disponibles->count() != 0 && count($tipos_habitacion) != count($config)) {
                        $prop->disponible = true;
                    } else {
                        $prop->disponible = false;
                    }
                }

                $retorno['errors'] = false;
                $retorno['msj']    = $propiedades;
            } catch (QueryException $e) {
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