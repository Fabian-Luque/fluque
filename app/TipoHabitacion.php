<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoHabitacion extends Model
{
    
	protected $table = 'tipo_habitacion';

	protected $fillable = ['nombre', 'capacidad', 'propiedad_id'];


	public function habitaciones(){


		return $this->hasMany('App\Habitacion', 'tipo_habitacion_id');


	}

	public function precios(){

		return $this->hasMany('App\PrecioTemporada', 'tipo_habitacion_id');


	}

	public function propiedad(){


        return $this->belongsTo('App\Propiedad', 'propiedad_id'); 


    }


}
