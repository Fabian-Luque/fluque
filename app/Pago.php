<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ZonaHoraria;
use JWTAuth;
use \Carbon\Carbon;

class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = ['monto_pago','monto_equivalente','tipo','numero_operacion','tipo_moneda_id','tipo_comprobante_id','caja_id','reserva_id', 'created_at'];

    protected $dates = ['created_at'];

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

	public function caja(){
		return $this->belongsTo('App\Caja', 'caja_id');
	}

	public function getCreatedAtAttribute($value)
    {
        $user            = JWTAuth::parseToken()->toUser();
        $propiedad       = $user->propiedad[0];
        $zona_horaria_id = $propiedad->zona_horaria_id;
        $zona_horaria    = ZonaHoraria::where('id', $zona_horaria_id)->first();
        $pais            = $zona_horaria->nombre;
        return Carbon::parse($value)->timezone($pais)->format('Y-m-d H:i:s');
    }   

}
