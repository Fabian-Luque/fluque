<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = 'regiones';

    public function pais(){

		return $this->belongsTo('App\Pais', 'pais_id');

	}
}
