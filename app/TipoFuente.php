<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoFuente extends Model
{
    
	protected $table = 'tipo_fuente';

	public function reservas(){

		return $this->hasMany('App\Reserva', 'tipo_fuente_id');

	}


}
