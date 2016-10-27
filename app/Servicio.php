<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Servicio extends Model
{
    

	use SoftDeletes;
    protected $table = 'servicios';

	protected $fillable = ['nombre', 'categoria', 'precio'];


	public function propiedad(){

		return $this->belongsTo('App\Propiedad', 'propiedad_id');

	}


}
