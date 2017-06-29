<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = ['monto_pago','monto_equivalente','tipo','numero_operacion','tipo_moneda_id','tipo_comprobante_id','reserva_id', 'created_at'];

	public function reserva(){

		return $this->belongsTo('App\Reserva', 'reserva_id');

	}

	public function tipoComprobante(){

		return $this->belongsTo('App\TipoComprobante', 'tipo_comprobante_id');

	}

	public function metodoPago(){

		return $this->belongsTo('App\MetodoPago', 'metodo_pago_id');

	}

	public function tipoMoneda(){

		return $this->belongsTo('App\TipoMoneda', 'tipo_moneda_id');

	}

	public function servicios(){

		return $this->hasMany('App\HuespedReservaServicio', 'pago_id');

	}

}
