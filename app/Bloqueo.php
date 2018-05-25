<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bloqueo extends Model
{
    protected $table = 'bloqueos';

    public function habitacion(){
		return $this->belongsTo('App\Habitacion', 'habitacion_id');
	}
}
