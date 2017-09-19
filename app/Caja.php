<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\ZonaHoraria;
use JWTAuth;
use \Carbon\Carbon;

class Caja extends Model
{
    use SoftDeletes;
    protected $table = 'cajas';

    protected $fillable = ['fecha_apertura', 'fecha_cierre', 'user_id', 'propiedad_id', 'estado_caja_id'];


    public function propiedad(){
    	return $this->belongsTo('App\Propiedad', 'propiedad_id'); 
    }

    public function user(){
    	return $this->belongsTo('App\User', 'user_id'); 
    }

    public function estadoCaja(){
    	return $this->belongsTo('App\EstadoCaja', 'estado_caja_id'); 
    }

    public function pagos(){
        return $this->hasMany('App\Pago', 'caja_id');
    }

    public function montos(){
        return $this->hasMany('App\MontoCaja', 'caja_id');
    }

    public function cajaEgresos(){
        return $this->belongsToMany('App\Egreso', 'egreso_caja')
        ->withPivot('id', 'monto', 'descripcion', 'tipo_moneda_id')
        ->withTimestamps();
    }

    public function getFechaAperturaAtAttribute($value)
    {
        $user            = JWTAuth::parseToken()->toUser();
        $propiedad       = $user->propiedad[0];
        $zona_horaria_id = $propiedad->zona_horaria_id;
        $zona_horaria    = ZonaHoraria::where('id', $zona_horaria_id)->first();
        $pais            = $zona_horaria['nombre'];
        return Carbon::parse($value)->timezone($pais)->format('Y-m-d H:i:s');
    }   



}