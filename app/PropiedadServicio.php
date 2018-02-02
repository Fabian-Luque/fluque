<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ZonaHoraria;
use JWTAuth;
use \Carbon\Carbon;

class PropiedadServicio extends Model
{
  	protected $table = 'metodo_pago_propiedad_servicio';

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
