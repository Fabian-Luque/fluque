<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reserva extends Model
{
    
	use SoftDeletes;
    protected $table = 'reservas';

	protected $fillable = ['monto_total','monto_sugerido','metodo_pago', 'ocupacion','fuente',' habitacion_id ' ,'cliente_id','checkin', 'checkout'];



	public function habitacion(){

		return $this->belongsTo('App\Habitacion', 'habitacion_id');


	}


	public function cliente(){

		return $this->belongsTo('App\Cliente', 'cliente_id');


	}


}
