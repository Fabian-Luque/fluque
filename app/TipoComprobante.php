<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoComprobante extends Model
{
    
	protected $table = 'tipo_comprobante';


	public function pagos(){

		return $this->hasMany('App\Pago', 'tipo_comprobante_id');

	}







}
