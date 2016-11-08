<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetalleNoche extends Model
{
    
	use SoftDeletes;
    protected $table = 'detalle_noches';

	protected $fillable = [];



	public function habitacion(){

		return $this->belongsTo('App\habitacion', 'habitacion_id');


	}

	public function reserva(){

		return $this->belongsTo('App\Reserva', 'reserva_id');


	}




}
