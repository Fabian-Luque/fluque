<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Habitacion extends Model
{
    
	/*use SoftDeletes;*/

	protected $table = 'habitaciones';

	protected $fillable = ['nombre' , 'tipo', 'precio'];

	public function propiedad(){

		return $this->belongsTo('App\Propiedad', 'propiedad_id');



	}

	public function equipamiento(){


		return $this->hasOne('App\Equipamiento', 'habitacion_id');


	}

	


}
