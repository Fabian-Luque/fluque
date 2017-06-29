<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HuespedReservaServicio extends Model
{
    

	protected $table = 'huesped_reserva_servicio';	

	protected $fillable = ['estado', 'pago_id'];

	public function pago(){

		return $this->belongsTo('App\Pago', 'pago_id');

	}


}
