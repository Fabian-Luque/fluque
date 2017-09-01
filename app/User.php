<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Carbon\Carbon;
use App\ZonaHoraria;
use JWTAuth;

class User extends Authenticatable
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['name', 'email', 'password', 'phone', 'rol_id', 'estado_id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function propiedad(){
        return $this->belongsToMany('App\Propiedad', 'propiedad_user'); //relacion muchos a muchos
    }

    public function rol(){
        return $this->belongsTo('App\Rol', 'rol_id');
    }

    public function estado(){
        return $this->belongsTo('App\Estado', 'estado_id');
    }

    public function setPasswordAttribute($value)
    {

        if(!empty($value))
        {
    
        $this->attributes['password'] = bcrypt($value);
    
        }

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
