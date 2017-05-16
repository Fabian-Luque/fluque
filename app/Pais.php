<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    protected $table = 'paises';

    public function regiones(){

		return $this->hasMany('App\Region', 'pais_id');


	}

}
