<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{


   	protected $table = 'categorias';


   	public function servicios(){

		return $this->hasMany('App\Servicio', 'categoria_id');

	}

	
}
