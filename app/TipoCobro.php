<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoCobro extends Model
{
    protected $table = 'tipo_cobro';


	public function propiedades(){

	return $this->hasMany('App\Propiedad', 'tipo_cobro_id');

	}
}
