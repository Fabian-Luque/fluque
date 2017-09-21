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

    public function egresosCaja(){
        return $this->hasMany('App\EgresoCaja', 'caja_id');
    }

    // public function cajaEgresos(){
    //     return $this->belongsToMany('App\Egreso', 'egreso_caja')
    //     ->withPivot('id', 'monto', 'descripcion', 'tipo_moneda_id')
    //     ->withTimestamps();
    // }



}