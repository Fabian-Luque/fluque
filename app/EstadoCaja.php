<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EstadoCaja extends Model
{

    protected $table = 'estado_caja';

    public function cajas(){
        return $this->hasMany('App\Caja', 'estado_caja_id');
    }

}
