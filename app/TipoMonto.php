<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoMonto extends Model
{
    protected $table = 'tipo_moneda';

    public function MontosCaja(){
        return $this->hasMany('App\MontoCaja', 'tipo_monto_id');
    }
    
}
