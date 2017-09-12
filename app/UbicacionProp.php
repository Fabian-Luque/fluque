<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;

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
}