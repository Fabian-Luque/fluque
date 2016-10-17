<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Propiedad extends Model
{
	use SoftDeletes;
    protected $table = 'propiedades';

	protected $fillable = [  'nombre','tipo', 'numero_habitaciones','pais','ciudad','estado','direccion', 'telefono', 'email', 'nombre_responsable', 'descripcion', 'moneda'];



    public function user(){


        return $this->belongsTo('App\User', 'user_id'); 


    }


}
