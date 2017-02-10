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

	public function propiedades(){


			return $this->belongsToMany('App\Propiedad', 'metodo_pago_propiedad_servicio')
			->withPivot('metodo_pago_id','cantidad', 'precio_total')
			->withTimestamps();


	}

	public function metodosPago(){


			return $this->belongsToMany('App\MetodoPago', 'metodo_pago_propiedad_servicio')
			->withPivot('propiedad_id','cantidad', 'precio_total')
			->withTimestamps();


	}

	public function clientePropiedades(){

            return $this->belongsToMany('App\Propiedad', 'cliente_propiedad_servicio')
            ->withPivot('cliente_id', 'nombre_consumidor','apellido_consumidor' ,'rut_consumidor','cantidad', 'precio_total')
            ->withTimestamps();

    }

   	public function propiedadClientes(){

            return $this->belongsToMany('App\Cliente', 'cliente_propiedad_servicio')
            ->withPivot('propiedad_id', 'nombre_consumidor','apellido_consumidor' ,'rut_consumidor','cantidad', 'precio_total')
            ->withTimestamps();

    }



}
