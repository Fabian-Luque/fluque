<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Precio extends Model
{
    protected $table = 'precios';

    protected $fillable = ['precio_habitacion', 'tipo_moneda_id'];


    public function habitacion(){

		return $this->belongsTo('App\Habitacion', 'habitacion_id');


	}

	public function TipoMoneda(){

		return $this->belongsTo('App\TipoMoneda', 'tipo_moneda_id');


	}



}
