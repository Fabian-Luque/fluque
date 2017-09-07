<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Calendario extends Model
{
    
	protected $table = 'calendarios';

	protected $fillable = ['fecha' , 'temporada_id'];

	public function temporada(){

		return $this->belongsTo('App\Temporada', 'temporada_id');


	}




}
