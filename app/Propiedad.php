<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Propiedad extends Model
{
	use SoftDeletes;
    protected $table = 'propiedades';

	protected $fillable = [ 'id', 'nombre','tipo', 'numero_habitaciones','region','pais','ciudad','estado','direccion', 'telefono', 'email', 'nombre_responsable', 'descripcion','iva', 'porcentaje_deposito', 'tipo_propiedad_id'];



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


    public function calificacionHuespedes(){

        return $this->belongsToMany('App\Huesped', 'huesped_propiedad')
        ->withPivot('comentario', 'calificacion')
        ->withTimestamps();

    }

    public function vendeServicios(){

            return $this->belongsToMany('App\Servicio', 'metodo_pago_propiedad_servicio')
            ->withPivot('metodo_pago_id', 'cantidad', 'precio_total')
            ->withTimestamps();



    }

    public function metodosPago(){

            return $this->belongsToMany('App\MetodoPago', 'metodo_pago_propiedad_servicio')
            ->withPivot('servicio_id', 'cantidad', 'precio_total')
            ->withTimestamps();



    }


}
