<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PasPago extends Model {
    protected $table = 'pasarela_pago';
    protected $fillable = [ 
    	'id', 
    	'nombre',
    	'pas_pago_id',
    	'procedencia'
    ];
}
