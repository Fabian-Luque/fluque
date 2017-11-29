<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CuentaBancaria extends Model
{

	protected $table = 'cuenta_bancaria';

	protected $fillable = ['nombre_banco', 'numero_cuenta', 'titular', 'rut', 'email', 'tipo_cuenta_id'];


    public function propiedad(){
        return $this->belongsTo('App\Propiedad', 'propiedad_id');
    }

    public function tipoCuenta(){
        return $this->belongsTo('App\TipoCuenta', 'tipo_cuenta_id');
    }



}
