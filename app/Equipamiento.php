<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipamiento extends Model
{
    
	use SoftDeletes;
	protected $table = 'equipamiento';

	protected $fillable = [ 'bano' , 'tv', 'wifi', 'frigobar', 'habitacion_id'];

	

	public function habitacion(){

		return $this->belongsTo('App\habitacion', 'habitacion_id');


	}

}
