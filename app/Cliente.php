<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    

	use SoftDeletes;
    protected $table = 'clientes';

	protected $fillable = ['nombre', 'apellido', 'email'];


	public function reservas(){

		return $this->hasMany('App\Reserva', 'cliente_id');


	}


}
