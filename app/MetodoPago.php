<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    

	protected $table = 'metodo_pago';

	public function reservas(){

		return $this->hasMany('App\Reserva', 'metodo_pago_id');

	}








}
