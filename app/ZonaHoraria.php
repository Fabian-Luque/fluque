<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ZonaHoraria extends Model
{
    protected $table = 'zona_horaria';

    public function propiedades(){

    	return $this->hasMany('App\Propiedad', 'zona_horaria_id');


    }
}
