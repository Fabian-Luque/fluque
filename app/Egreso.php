<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Egreso extends Model
{
   protected $table = 'egresos';

    protected $fillable = ['nombre', 'propiedad_id'];

    public function propiedad() {
        return $this->belongsTo('App\Propiedad', 'propiedad_id');
    }

    public function egresosPropiedades(){
		return $this->belongsToMany('App\Propiedad', 'egreso_propiedad')
		->withPivot('id', 'monto', 'descripcion')
		->withTimestamps();
	}

	public function egresosCajas(){
		return $this->belongsToMany('App\Caja', 'egreso_caja')
		->withPivot('id', 'monto', 'descripcion')
		->withTimestamps();
	}
}
