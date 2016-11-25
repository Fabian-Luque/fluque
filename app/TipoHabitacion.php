<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoHabitacion extends Model
{
    
	protected $table = 'tipo_habitacion';


	public function habitaciones(){


		return $this->hasMany('App\Habitacion', 'tipo_habitacion_id');


	}


}
