<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ColorMotor extends Model
{
    protected $table = 'colores_motor';

	protected $fillable = ['nombre', 'color', 'background_color'];

	public function propiedades(){
        return $this->belongsToMany('App\Propiedad', 'motor_propiedad')
        ->withPivot('clasificacion_color_id')
        ->withTimestamps();

    }

    public function clasificacionColores(){
        return $this->belongsToMany('App\ClasificacionColor', 'motor_propiedad')
        ->withPivot('propiedad_id')
        ->withTimestamps();
    }


}
