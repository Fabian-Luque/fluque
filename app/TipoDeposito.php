<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoDeposito extends Model
{

	protected $table = 'tipo_deposito';

	protected $fillable = ['valor', 'propiedad_id', 'tipo_deposito_id'];

	public function propiedadesTipoDeposito(){
        return $this->hasMany('App\PropiedadTipoDeposito', 'tipo_deposito_id');
    }


}
