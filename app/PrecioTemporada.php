<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrecioTemporada extends Model
{
    protected $table = 'precios_temporada';

	protected $fillable = ['precio', 'tipo_habitacion_id', 'tipo_moneda_id', 'temporada_id'];


	

	public function tipoHabitacion(){

		return $this->belongsTo('App\TipoHabitacion', 'tipo_habitacion_id');


	}

	public function tipoMoneda(){

		return $this->belongsTo('App\TipoMoneda', 'tipo_moneda_id');


	}

	public function temporada(){

		return $this->belongsTo('App\Temporada', 'temporada_id');


	}


}
