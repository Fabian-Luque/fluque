<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Propiedad extends Model
{
    protected $table = 'propiedades';

	protected $fillable = [  'nombre','tipo', 'numero_habitaciones','ciudad','direccion'];



    public function user(){


        return $this->belongsTo('App\User', 'user_id'); 


    }


}
