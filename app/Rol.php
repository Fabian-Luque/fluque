<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rol extends Model
{
    use SoftDeletes;
	protected $dates = ['deleted_at'];
    protected $table = 'roles';

    protected $fillable = array('nombre');

	public function permisos(){
		return $this->belongsToMany('App\Permiso', 'permiso_rol')
			->withPivot('id', 'estado');
	}

	public function propiedad(){
        return $this->belongsTo('App\Propiedad', 'propiedad_id');
    }

    public function users(){
        return $this->hasMany('App\User', 'rol_id');
    }
}
