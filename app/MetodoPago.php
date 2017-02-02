<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    

	protected $table = 'metodo_pago';

	public function reservas(){

		return $this->hasMany('App\Reserva', 'metodo_pago_id');

	}


	public function propiedades(){


		return $this->belongsToMany('App\Propiedad', 'metodo_pago_propiedad_servicio')
			->withPivot('servicio_id','cantidad', 'precio_total')
			->withTimestamps();


	}

	public function servicios(){


		return $this->belongsToMany('App\Propiedad', 'metodo_pago_propiedad_servicio')
			->withPivot('propiedad_id','cantidad', 'precio_total')
			->withTimestamps();


	}





}
