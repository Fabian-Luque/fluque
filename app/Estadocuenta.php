<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Estadocuenta extends Model {
    protected $table = 'estado_cuenta';
	protected $fillable = [ 
		'id', 
		'nombre', 
		'created_at',
		'updated_at'
	];

	public function propiedades(){
		return $this->hasMany(
			'App\Propiedad', 
			'estado_cuenta_id'
		);
	}
}