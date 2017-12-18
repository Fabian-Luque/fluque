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
        'prop_id'
    ];

    protected $spatialFields = [
        'location',
    ];

    public function propiedad() {
  		return $this->hasOne('App\Propiedad');
	}

    public static function getPropiedadesCercanas($latitud, $longitud, $radio) {
        return DB::select(
            'CALL propiedades_cercanas(?, ?, ?)', 
            array(
                $latitud,
                $longitud,
                $radio // KM ^ 2
            )
        );
    }
}