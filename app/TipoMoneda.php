<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoMoneda extends Model
{
    protected $table = 'tipo_moneda';


	public function precioServicios(){
		return $this->hasMany('App\PrecioServicio', 'tipo_moneda_id');
	}

	public function reservas(){
		return $this->hasMany('App\Reserva', 'tipo_moneda_id');
	}

    public function pagos(){
        return $this->hasMany('App\Pago', 'tipo_moneda_id');
    }

    public function preciosTemporada(){
        return $this->hasMany('App\PrecioTemporada', 'tipo_moneda_id');
    }

    public function montosCaja(){
        return $this->hasMany('App\MontoCaja', 'tipo_moneda_id');
    }

    public function egresosPropiedad(){
        return $this->hasMany('App\EgresoPropiedad', 'tipo_moneda_id');
    }

    public function egresosCaja(){
        return $this->hasMany('App\EgresoCaja', 'tipo_moneda_id');
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
