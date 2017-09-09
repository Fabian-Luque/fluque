<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoPropiedad extends Model {

	protected $table = 'tipo_propiedad';
 
	public function propiedades() {
		return $this->hasMany('App\Propiedad', 'tipo_propiedad_id');
	}
}