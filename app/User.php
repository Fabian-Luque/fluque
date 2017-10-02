<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Carbon\Carbon;
use App\ZonaHoraria;
use JWTAuth;

class User extends Authenticatable {
    use SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'rol_id', 'estado_id'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function propiedad(){
        return $this->belongsToMany(
            'App\Propiedad', 
            'propiedad_user',
            'user_id',
            'propiedad_id'
        ); //relacion muchos a muchos
    }
   
    public function rol(){
        return $this->belongsTo('App\Rol', 'rol_id');
    }

    public function estado(){
        return $this->belongsTo('App\Estado', 'estado_id');
    }


    public function cajas(){
        return $this->hasMany('App\Caja', 'user_id');
    }

    public function setPasswordAttribute($value) {
        if(!empty($value)) { 
            $this->attributes['password'] = bcrypt($value);
        }
    }

    public function VerifyPassword($value) {
        if (password_verify($value, $this->attributes['password'])) {
            return 1;
        } else {
            return 0;
        }
    }

    public function getRules() {
        $rules = array(
            'name'                => 'required',
            'email'               => 'required|unique:users,email',
            'phone'               => 'required',
            'password'            => 'required|min:6',
            'nombre'              => 'required',
            'tipo_propiedad_id'   => 'required|numeric',
            'numero_habitaciones' => 'required|numeric',
            'ciudad'              => 'required',
            'direccion'           => 'required',
        );
        return $rules;
    }

/*    public function getCreatedAtAttribute($value)
    {
        $user = JWTAuth::parseToken()->toUser();
        $propiedad = $user->propiedad;
        $zona_horaria    = ZonaHoraria::where('id', $propiedad->zona_horaria_id)->first();
        $pais            = $zona_horaria->nombre;
        return Carbon::parse($value)->timezone($pais)->format('Y-m-d H:i:s');
    }   */
}