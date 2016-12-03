<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Huesped extends Model
{
    
	protected $table = 'huespedes';

	protected $fillable = ['nombre', 'apellido', 'rut', 'pais', 'email', 'telefono', 'reserva_id'];


	public function reservas(){

		return $this->belongsToMany('App\Reserva', 'huesped_reserva');


	}




}
