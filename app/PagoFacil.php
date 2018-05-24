<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PagoFacil extends Model {
    protected $table = 'pago_facil';
    protected $fillable = [ 
    	'id', 
    	'order_id',
    	'monto',
    	'email',
    	'status',
    	'pago_id',
    	'created_at',
    	'updated_at'
    ];
        
    public function pagoOnline() {
        return $this->hasOne('App\PagoOnline', 'pago_id');
    }

    public function getCreatedAtAttribute($value)
    {
        $token = JWTAuth::getToken();
        if ($token) {
        $user            = JWTAuth::parseToken()->toUser();
        $propiedad       = $user->propiedad[0];
        $zona_horaria_id = $propiedad->zona_horaria_id;
        $zona_horaria    = ZonaHoraria::where('id', $zona_horaria_id)->first();
        $pais            = $zona_horaria['nombre'];
        return Carbon::parse($value)->timezone($pais)->format('Y-m-d H:i:s');
        } 
        

    }   
    
}