<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mensajeria extends Model {
   
    protected $table = 'mensajeria';
	protected $fillable = [
		'id', 
		'emisor_id', 
		'receptor_id', 
		'mensaje', 
		'created_at'
	];
}