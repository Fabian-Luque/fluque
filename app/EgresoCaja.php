<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EgresoCaja extends Model
{
 	protected $table = 'egreso_caja';

    protected $fillable = ['monto', 'descripcion', 'egreso_id', 'caja_id', 'user_id', 'tipo_moneda_id'];

  	public function caja(){
        return $this->belongsTo('App\Caja', 'caja_id');
    }

  	public function egreso(){
        return $this->belongsTo('App\Egreso', 'egreso_id');
    }

  	public function tipoMoneda(){
        return $this->belongsTo('App\TipoMoneda', 'tipo_moneda_id');
    }
}
