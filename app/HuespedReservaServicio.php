<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ZonaHoraria;
use JWTAuth;
use \Carbon\Carbon;


class HuespedReservaServicio extends Model
{
    

	protected $table = 'huesped_reserva_servicio';	

	protected $fillable = ['estado', 'pago_id'];

	public function pago(){
		return $this->belongsTo('App\Pago', 'pago_id');
	}

	public function reserva(){
		return $this->belongsTo('App\Reserva', 'reserva_id');
	}


}
