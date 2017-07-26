<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    

	use SoftDeletes;
    protected $table = 'clientes';

	protected $fillable = ['nombre', 'apellido','rut', 'direccion','ciudad','telefono', 'email', 'giro', 'pais_id', 'region_id', 'tipo_cliente_id'];


	public function reservas(){

		return $this->hasMany('App\Reserva', 'cliente_id');


	}

    public function pais(){


        return $this->belongsTo('App\Pais', 'pais_id'); 


    }

    public function region(){


        return $this->belongsTo('App\Region', 'region_id'); 


    }

	public function tipoCliente(){

		return $this->belongsTo('App\TipoCliente', 'tipo_cliente_id');

	}

	public function servicios(){

            return $this->belongsToMany('App\Servicio', 'cliente_propiedad_servicio')
            ->withPivot('propiedad_id', 'nombre_consumidor','apellido_consumidor' ,'rut_consumidor','cantidad', 'precio_total')
            ->withTimestamps();

    }


    public function propiedades(){

            return $this->belongsToMany('App\Propiedad', 'cliente_propiedad_servicio')
            ->withPivot('servicio_id','nombre_consumidor','apellido_consumidor' ,'rut_consumidor','cantidad', 'precio_total')
            ->withTimestamps();

    }


}
