<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{

    protected $table = 'estado';

    protected $fillable = array('nombre');

    public function users(){
        return $this->hasMany('App\User', 'estado_id');
    }



}
