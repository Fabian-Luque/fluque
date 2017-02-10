<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reserva extends Model
{
    
	use SoftDeletes;
    protected $table = 'reservas';

	protected $fillable = ['monto_alojamiento','noches','monto_consumo','monto_total','monto_sugerido','metodo_pago_id','monto_por_pagar', 'ocupacion','tipo_fuente_id',' habitacion_id ' ,'cliente_id','checkin', 'checkout','estado_reserva_id'];



	public function habitacion(){

		return $this->belongsTo('App\Habitacion', 'habitacion_id');

	}


	public function cliente(){

		return $this->belongsTo('App\Cliente', 'cliente_id');

	}

	public function huespedes(){

		return $this->belongsToMany('App\Huesped', 'huesped_reserva');

	}

	public function tipoFuente(){

		return $this->belongsTo('App\TipoFuente', 'tipo_fuente_id');

	}

	public function metodoPago(){

		return $this->belongsTo('App\MetodoPago', 'metodo_pago_id');

	}

	public function estadoReserva(){

		return $this->belongsTo('App\EstadoReserva', 'estado_reserva_id');

	}

	public function pagos(){

		return $this->hasMany('App\Pago', 'reserva_id');

	}

	public function reservasHuespedes(){


		return $this->belongsToMany('App\Huesped', 'huesped_reserva_servicio')
				->withPivot('servicio_id','cantidad', 'precio_total','estado')
				->withTimestamps();

	}

	public function servicios(){

		return $this->belongsToMany('App\Servicio', 'huesped_reserva_servicio')
				->withPivot('huesped_id','cantidad', 'precio_total', 'estado')
				->withTimestamps();
				

	}

}