<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EgresoPropiedad extends Model
{
    protected $table = 'egreso_propiedad';

    protected $fillable = ['monto', 'descripcion', 'egreso_id', 'propiedad_id', 'user_id', 'tipo_moneda_id'];

    public function moneda(){
        return $this->belongsTo('App\TipoMoneda', 'tipo_moneda_id');
    }

}
