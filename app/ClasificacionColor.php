<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClasificacionColor extends Model
{
    protected $table = 'clasificacion_color';

    public function propiedades(){
        return $this->belongsToMany('App\Propiedad', 'motor_propiedad')
        ->withPivot('color_motor_id')
        ->withTimestamps();
    }

    public function coloresMotor(){
        return $this->belongsToMany('App\ColorMotor', 'motor_propiedad')
        ->withPivot('propiedad_id')
        ->withTimestamps();
    }
}
