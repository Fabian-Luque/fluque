<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MotorPropiedad extends Model
{
    protected $table = 'motor_propiedad';

    protected $fillable = ['propiedad_id', 'color_motor_id', 'clasificacion_color_id'];
}
