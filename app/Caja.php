<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Caja extends Model
{
    use SoftDeletes;
    protected $table = 'propiedades';

    public function propiedad(){
    	return $this->belongsTo('App\Propiedad', 'propiedad_id'); 
    }

    public function user(){
    	return $this->belongsTo('App\User', 'user_id'); 
    }

    public function estadoCaja(){
    	return $this->belongsTo('App\EstadoCaja', 'estado_caja_id'); 
    }

    public function reservas(){
        return $this->hasMany('App\Reserva', 'caja_id');
    }

    public function tipoMoneda(){
    	return $this->belongsTo('App\TipoMoneda', 'tipo_moneda_id'); 
    }
}