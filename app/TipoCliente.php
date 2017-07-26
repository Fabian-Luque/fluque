<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoCliente extends Model
{
    
	protected $table = 'tipo_cliente';

	public function clientes(){

		return $this->hasMany('App\Cliente', 'tipo_cliente_id');

	}


}
