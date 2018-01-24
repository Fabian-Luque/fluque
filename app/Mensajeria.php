<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ZonaHoraria;
use JWTAuth;
use \Carbon\Carbon;

class Mensajeria extends Model {
   
    protected $table = 'mensajeria';
	protected $fillable = [
		'id', 
		'emisor_id', 
		'receptor_id', 
		'mensaje', 
        'estado',
		'created_at'
	];

	public function propiedad_emisor() {
        return $this->belongsTo('App\Propiedad', 'emisor_id'); 
    }

    public function propiedad_receptor() {
        return $this->belongsTo('App\Propiedad', 'receptor_id'); 
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