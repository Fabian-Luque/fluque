<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PropiedadMoneda extends Model
{
    protected $table = 'propiedad_moneda';

    protected $fillable = ['clasificacion_moneda_id', 'tipo_moneda_id'];

}
