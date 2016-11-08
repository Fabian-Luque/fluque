<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reserva extends Model
{
    
	use SoftDeletes;
    protected $table = 'reservas';

	protected $fillable = ['precio_total', 'ocupacion', 'cliente_id','checkin', 'checkout'];



	public function detalleNoches(){

		return $this->hasMany('App\DetalleNoche', 'reserva_id');


	}

	public function cliente(){

		return $this->belongsTo('App\Cliente', 'cliente_id');


	}


}
