<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ZonaHoraria;
use JWTAuth;
use \Carbon\Carbon;

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

    public function getCreatedAtAttribute($value)
    {
        $user            = JWTAuth::parseToken()->toUser();
        $propiedad       = $user->propiedad[0];
        $zona_horaria_id = $propiedad->zona_horaria_id;
        $zona_horaria    = ZonaHoraria::where('id', $zona_horaria_id)->first();
        $pais            = $zona_horaria['nombre'];
        return Carbon::parse($value)->timezone($pais)->format('Y-m-d H:i:s');
    }   
}
