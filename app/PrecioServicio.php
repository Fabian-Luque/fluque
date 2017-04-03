<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrecioServicio extends Model
{
    protected $table = 'precios_servicio';

    protected $fillable = ['precio_servicio'];


    public function servicio(){

		return $this->belongsTo('App\Servicio', 'servicio_id');


	}

	public function TipoMoneda(){

		return $this->belongsTo('App\TipoMoneda', 'tipo_moneda_id');


	}





}
