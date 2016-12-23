<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = ['monto_pago','tipo','reserva_id'];

	public function reserva(){

		return $this->belongsToMany('App\Reserva', 'reserva_id');

	}

}
