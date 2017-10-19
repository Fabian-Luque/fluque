<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QvoUser extends Model {
    protected $table = 'qvousers';

    protected $fillable = [ 
    	'id', 
    	'user_id', 
    	'qvo_id',
    	'created_at', 
    	'updated_at'
    ];
 
	public function User() {
        return $this->hasOne('App\User');
    }
}