<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Servicio extends Model
{
    

	use SoftDeletes;
    protected $table = 'servicios';

	protected $fillable = ['nombre', 'categoria', 'precio'];


	public function propiedad(){

		return $this->belongsTo('App\Propiedad', 'propiedad_id');

	}


	public function reservas(){


		return $this->belongsToMany('App\Reserva', 'huesped_reserva_servicio')
			->withPivot('id','huesped_id','cantidad', 'precio_total')
			->withTimestamps();




	}

	public function huespedes(){

		return $this->belongsToMany('App\Huesped', 'huesped_reserva_servicio')
			->withPivot('reserva_id','cantidad', 'precio_total')
			->withTimestamps();



	}


}
