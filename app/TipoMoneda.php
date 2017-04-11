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

	public function propiedades(){

            return $this->belongsToMany('App\Propiedad', 'propiedad_moneda')
            ->withPivot('clasificacion_moneda_id')
            ->withTimestamps();



    }

    public function clasificacionMonedas(){

            return $this->belongsToMany('App\ClasificacionMoneda', 'propiedad_moneda')
            ->withPivot('propiedad_id')
            ->withTimestamps();



    }
}
