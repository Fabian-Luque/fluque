<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Calendario extends Model
{
    
	protected $table = 'calendarios';

	protected $fillable = ['disponibilidad' , 'reservas', 'precio', 'fecha', 'habitacion_id'];


	public function habitacion(){

		return $this->belongsTo('App\habitacion', 'habitacion_id');


	}


}
