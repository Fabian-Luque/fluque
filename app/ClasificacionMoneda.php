<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClasificacionMoneda extends Model
{
    protected $table = 'clasificacion_moneda';

    public function propiedades(){
        return $this->belongsToMany('App\Propiedad', 'propiedad_moneda')
        ->withPivot('tipo_moneda_id')
        ->withTimestamps();
    }

    public function tipoMonedas(){
        return $this->belongsToMany('App\TipoMoneda', 'propiedad_moneda')
        ->withPivot('propiedad_id')
        ->withTimestamps();
    }
}
