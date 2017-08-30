<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permiso extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'permisos';

    protected $fillable = array('nombre');

	public function roles(){
		return $this->belongsToMany('App\Rol', 'permiso_rol')
			->withPivot('estado');
	}

	public function seccion(){
        return $this->belongsTo('App\Seccion', 'seccion_id');
    }

}
