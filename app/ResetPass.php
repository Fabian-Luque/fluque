<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResetPass extends Model {
	protected $table = 'password_resets';
	protected $primaryKey = 'email';
	public $timestamps = false;

	protected $fillable = [ 
		'email', 
		'token', 
		'created_at'
	];
}