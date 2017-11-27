<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoCuenta extends Model
{

	protected $table = 'tipo_cuenta';

	protected $fillable = ['nombre'];

    public function cuentasBancaria(){
        return $this->hasMany('App\CuentaBancaria', 'tipo_cuenta_id');
    }
}
