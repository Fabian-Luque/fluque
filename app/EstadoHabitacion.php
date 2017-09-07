<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EstadoHabitacion extends Model
{
    protected $table = 'estado_habitacion';

	public function habitaciones(){

		return $this->hasMany('App\Habitacion', 'estado_habitacion_id');

	}
}
