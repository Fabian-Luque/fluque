<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = 'regiones';

    public function pais(){

		return $this->belongsTo('App\Pais', 'pais_id');

	}

	public function propiedades(){

		return $this->hasMany('App\Propiedad', 'region_id');


	}

	public function clientes(){

		return $this->hasMany('App\Cliente', 'region_id');


	}

	public function huespedes(){

		return $this->hasMany('App\Huesped', 'pais_id');


	}


}
