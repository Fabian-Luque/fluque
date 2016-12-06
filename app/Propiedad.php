<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Propiedad extends Model
{
	use SoftDeletes;
    protected $table = 'propiedades';

	protected $fillable = [  'nombre','tipo', 'numero_habitaciones','region','pais','ciudad','estado','direccion', 'telefono', 'email', 'nombre_responsable', 'descripcion','iva', 'porcentaje_deposito'];



    public function user(){


        return $this->belongsTo('App\User', 'user_id'); 


    }

    public function habitaciones(){

    	return $this->hasMany('App\Habitacion', 'propiedad_id');


    }

    public function servicios(){

    	return $this->hasMany('App\Servicio', 'propiedad_id');

    }

    public function tipoPropiedad(){

        return $this->belongsTo('App\TipoPropiedad', 'tipo_propiedad_id');


    }


}
