<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Temporada extends Model
{
    protected $table = 'temporadas';

	protected $fillable = ['nombre', 'color', 'propiedad_id'];


	public function propiedad(){

		return $this->belongsTo('App\Propiedad', 'propiedad_id');


	}

	public function calendarios(){

		return $this->hasMany('App\Calendario', 'temporada_id');


	}

	public function precios(){

		return $this->hasMany('App\PrecioTemporada', 'temporada_id');



	}
}
