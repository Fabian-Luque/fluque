<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EstadoServicio extends Model
{

	protected $table = 'estado_servicio';

    public function servicios(){

		return $this->hasMany('App\Servicio', 'estado_servicio_id');

	}
}
