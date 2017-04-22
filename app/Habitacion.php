<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Habitacion extends Model
{
    
	/*use SoftDeletes;*/

	protected $table = 'habitaciones';

	protected $fillable = ['nombre', 'precio_base', 'disponibilidad_base', 'piso','estado_habitacion_id','tipo_habitacion_id'];

	public function propiedad(){

		return $this->belongsTo('App\Propiedad', 'propiedad_id');



	}

	public function equipamiento(){


		return $this->hasOne('App\Equipamiento', 'habitacion_id');


	}

	public function calendarios(){

		return $this->hasMany('App\Calendario', 'habitacion_id');


	}

	public function reservas(){

		return $this->hasMany('App\Reserva', 'habitacion_id');


	}

	public function tipoHabitacion(){

		return $this->belongsTo('App\TipoHabitacion', 'tipo_habitacion_id');


	}

	public function precios(){

		return $this->hasMany('App\Precio', 'habitacion_id');


	}

	public function estado(){

		return $this->belongsTo('App\EstadoHabitacion', 'estado_habitacion_id');


	}
	


}
