<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use DB;

class UbicacionProp extends Model {
    use SpatialTrait;

    protected $table = 'ubicacion_propiedad';

    protected $fillable = [
        'id',
        'prop_id',
        'location'
    ];

    protected $spatialFields = [
        'location',
    ];

    public function propiedad() {
  		return $this->hasOne('App\Propiedad');
	}

    public static function getPropiedadesCercanas($latitud, $longitud, $radio, $prop_id) {
        return DB::select(
            'SELECT id, prop_id, X(location) as latitud, Y(location) as longitud, created_at, updated_at FROM ubicacion_propiedad WHERE haversine(location,?, ?) < ? AND NOT prop_id = ?', 
            array(
                $latitud,
                $longitud,
                $radio, // KM ^ 2
                $prop_id
            )
        );
    }
}