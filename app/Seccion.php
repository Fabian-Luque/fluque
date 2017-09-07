<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
  	protected $dates = ['deleted_at'];
    protected $table = 'secciones';

	protected $fillable = array('nombre');

	public function permisos(){
        return $this->hasMany('App\Permiso', 'seccion_id');
    }
    
}
