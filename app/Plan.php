<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model {
    protected $table = 'planes';

    protected $fillable = [ 
    	'id', 
    	'facturacion',
    	'porcentaje_desc',
    	'plan_id'
    ];
}
