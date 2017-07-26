<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoComprobante extends Model
{
    
	protected $table = 'tipo_comprobante';


	public function pagos(){

		return $this->hasMany('App\Pago', 'tipo_comprobante_id');

	}


	public function propiedades(){


			return $this->belongsToMany('App\Propiedad', 'metodo_pago_propiedad_servicio')
			->withPivot('metodo_pago_id','servicio_id','cantidad', 'precio_total', 'numero_operacion')
			->withTimestamps();


	}

	public function servicios(){


		return $this->belongsToMany('App\Propiedad', 'metodo_pago_propiedad_servicio')
			->withPivot('propiedad_id','metodo_pago_id','cantidad', 'precio_total', 'numero_operacion')
			->withTimestamps();


	}

	public function metodosPago(){


			return $this->belongsToMany('App\MetodoPago', 'metodo_pago_propiedad_servicio')
			->withPivot('propiedad_id', 'servicio_id' ,'cantidad', 'precio_total', 'numero_operacion')
			->withTimestamps();


	}




}
