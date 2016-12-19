<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    

	use SoftDeletes;
    protected $table = 'clientes';

	protected $fillable = ['nombre','rut', 'direccion','ciudad','pais','telefono', 'email', 'giro', 'tipo_cliente_id','calificacion_promedio'];


	public function reservas(){

		return $this->hasMany('App\Reserva', 'cliente_id');


	}

	public function tipoCliente(){

		return $this->belongsTo('App\TipoCliente', 'tipo_cliente_id');

	}

	public function calificacionPropiedades(){

        return $this->belongsToMany('App\Propiedad', 'cliente_propiedad')
        ->withPivot('comentario', 'calificacion')
    	->withTimestamps();



    }


}
