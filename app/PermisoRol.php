<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PermisoRol extends Model
{
	protected $table 	= 'permiso_rol';
    protected $fillable = ['permiso_id', 'rol_id', 'estado'];
}
