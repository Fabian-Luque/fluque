<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Temporada extends Model
{
    protected $table = 'temporadas';

	protected $fillable = ['nombre', 'fecha_inicio', 'fecha_fin', 'propiedad_id'];
}
