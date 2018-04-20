<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model {
    protected $table = 'planes';

    protected $fillable = [ 
    	'id', 
    	'facturacion',
    	'plan_id'
    ];
}
