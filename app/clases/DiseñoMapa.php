<?php
namespace App\clases;
 
class DiseñoMapa {
  
    public function getDis() {
        $diseño = array(
    array(
        "name"=>"Mi Diseño", 
        "definition" => array(
            "featureType" => "water",
            "stylers" => array(
                array(
                    "saturation" => 43,
                    "lightness" => -11,
                    "hue" => "#0088ff"
                )
            ),array(
                "featureType" => "road",
                "elementType" => "geometry.fill",
                "stylers" => array(
                    array(
                        "hue" => "#ff0000",                      
                        "saturation" => -100,
                        "lightness" => 99
                    )
                )
            ),array(
                "featureType" => "road",
                "elementType" => "geometry.stroke",
                "stylers" => array(
                    array(
                        "color" => "#808080",
                        "lightness" => 54
                    )
                )
            ),array(
                "featureType" => "landscape.man_made",
                "elementType" => "geometry.fill",
                "stylers" => array(
                    array(
                        "color" => "#ece2d9"
                    )
                )
            ),array(
                "featureType" => "poi.park",
                "elementType" => "geometry.fill",
                "stylers" => array(
                    array(
                        "color" => "#ccdca1"
                    )
                )
            ),array(
                "featureType" => "road",
                "elementType" => "labels.text.fill",
                "stylers" => array(
                    array(
                        "color" => "#767676"
                    )
                )
            ),array(
                "featureType" => "road",
                "elementType" => "labels.text.stroke",
                "stylers" => array(
                    array(
                        "color" => "#ffffff"
                    )
                )
            ),array(
                "featureType" => "poi",
                "stylers" => array(
                    array(
                        "visibility" => "off"
                    )
                )
            ),array(
                "featureType" => "landscape.natural",
                "elementType" => "geometry.fill",
                "stylers" => array(
                    array(
                        "visibility" => "on",
                        "color" => "#b8cb93"
                    )
                )
            ),array(
                "featureType" => "poi.park",
                "stylers" => array(
                    array(
                        "visibility" => "on"
                    )
                )
            ),array(
                "featureType" => "poi.sports_complex",
                "stylers" => array(
                    array(
                        "visibility" => "on"
                    )
                )
            ),array(
                "featureType" => "poi.medical",
                "stylers" => array(
                    array(
                        "visibility" => "on"
                    )
                )
            ),array(
                "featureType" => "poi.business",
                "stylers" => array(
                    array(
                        "visibility" => "simplified"
                    )
                )
            )
        )
    )
);
        return $diseño;
    }
}



















