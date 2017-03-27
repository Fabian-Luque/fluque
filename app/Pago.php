<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = ['monto_pago','tipo' ,'numero_operacion' ,'tipo_comprobante_id','reserva_id'];

	public function reserva(){

		return $this->belongsToMany('App\Reserva', 'reserva_id');

	}

	public function tipoComprobante(){

		return $this->belongsTo('App\TipoComprobante', 'tipo_comprobante_id');

	}

	public function metodoPago(){

		return $this->belongsTo('App\MetodoPago', 'metodo_pago_id');

	}

}
