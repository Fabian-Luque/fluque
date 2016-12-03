<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reserva extends Model
{
    
	use SoftDeletes;
    protected $table = 'reservas';

	protected $fillable = ['monto_total','monto_sugerido','metodo_pago_id', 'ocupacion','tipo_fuente_id',' habitacion_id ' ,'cliente_id','checkin', 'checkout','estado_reserva_id'];



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



}
