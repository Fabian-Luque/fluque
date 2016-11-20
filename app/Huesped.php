<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Huesped extends Model
{
    
	protected $table = 'huespedes';

	protected $fillable = ['nombre', 'apellido', 'rut', 'pais', 'email', 'telefono', 'reserva_id'];


	public function reserva(){

		return $this->belongsTo('App\Reserva', 'reserva_id');


	}




}
