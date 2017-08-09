<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Estadocuenta extends Model {
    
    protected $table = 'estado_cuenta';

	protected $fillable = [ 
		'id', 
		'usuario_id', 
		'estado', 
		'created_at',
		'updated_at'
	];

}