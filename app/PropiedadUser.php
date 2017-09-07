<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PropiedadUser extends Model {
    protected $table 	= 'propiedad_user';
    protected $fillable = [
    	'propiedad_id', 
    	'user_id'
    ];
}
