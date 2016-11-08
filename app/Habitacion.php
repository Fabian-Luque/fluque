<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Habitacion extends Model
{
    
	/*use SoftDeletes;*/

	protected $table = 'habitaciones';

	protected $fillable = ['nombre' , 'tipo', 'precio_base', 'disponibilidad_base', 'piso'];

	public function propiedad(){

		return $this->belongsTo('App\Propiedad', 'propiedad_id');



	}

	public function equipamiento(){


		return $this->hasOne('App\Equipamiento', 'habitacion_id');


	}

	public function calendarios(){

		return $this->hasMany('App\Calendario', 'habitacion_id');


	}

	public function detalleNoches(){

		return $this->hasMany('App\DetalleNoche', 'habitacion_id');


	}

	


}