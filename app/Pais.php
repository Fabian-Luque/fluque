<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    protected $table = 'paises';

    public function regiones(){

		return $this->hasMany('App\Region', 'pais_id');


	}

	public function propiedades(){

		return $this->hasMany('App\Propiedad', 'pais_id');


	}

	public function clientes(){

		return $this->hasMany('App\Cliente', 'pais_id');


	}

	public function huespedes(){

		return $this->hasMany('App\Huesped', 'pais_id');


	}



}
