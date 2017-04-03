<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoMoneda extends Model
{
    protected $table = 'tipo_moneda';

    public function precios(){

		return $this->hasMany('App\Precio', 'tipo_moneda_id');


	}

	public function precioServicios(){

		return $this->hasMany('App\PrecioServicio', 'tipo_moneda_id');


	}

	public function reservas(){

		return $this->hasMany('App\Reserva', 'tipo_moneda_id');


	}
}
