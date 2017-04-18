<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    

	protected $table = 'metodo_pago';

	public function reservas(){

		return $this->hasMany('App\Reserva', 'metodo_pago_id');

	}

	public function pagos(){

		return $this->hasMany('App\Pago', 'metodo_pago_id');

	}


	public function propiedades(){


		return $this->belongsToMany('App\Propiedad', 'metodo_pago_propiedad_servicio')
			->withPivot('servicio_id', 'tipo_comprobate_id','cantidad', 'precio_total', 'numero_operacion')
			->withTimestamps();


	}

	public function servicios(){


		return $this->belongsToMany('App\Propiedad', 'metodo_pago_propiedad_servicio')
			->withPivot('propiedad_id','tipo_comprobate_id','cantidad', 'precio_total', 'numero_operacion')
			->withTimestamps();


	}

    public function tiposComprobante(){

            return $this->belongsToMany('App\TipoComprobante', 'metodo_pago_propiedad_servicio')
            ->withPivot('servicio_id','propiedad_id' , 'cantidad', 'precio_total', 'numero_operacion')
            ->withTimestamps();



    }





}
