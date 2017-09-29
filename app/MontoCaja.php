<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MontoCaja extends Model
{
    protected $table = 'montos_caja';

    protected $fillable = ['monto', 'caja_id', 'tipo_moneda_id', 'tipo_monto_id'];

    public function caja(){
    	return $this->belongsTo('App\Caja', 'caja_id');
    }

    public function tipoMoneda(){
    	return $this->belongsTo('App\TipoMoneda', 'tipo_moneda_id'); 
    }

    public function tipoMonto(){
    	return $this->belongsTo('App\TipoMonto', 'tipo_monto_id'); 
    }


}
