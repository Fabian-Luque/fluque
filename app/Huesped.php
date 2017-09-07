<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Huesped extends Model
{
    
	protected $table = 'huespedes';

	protected $fillable = ['nombre', 'apellido', 'rut', 'pais', 'email', 'telefono', 'reserva_id','calificacion_promedio', 'pais_id', 'region_id'];


	public function reservas(){

		return $this->belongsToMany('App\Reserva', 'huesped_reserva');


	}

	public function pais(){


        return $this->belongsTo('App\Pais', 'pais_id'); 


    }

    public function region(){


        return $this->belongsTo('App\Region', 'region_id'); 


    }

	public function huespedesReservas(){


		return $this->belongsToMany('App\Reserva', 'huesped_reserva_servicio')
			->withPivot('servicio_id','cantidad', 'precio_total', 'estado')
			->withTimestamps();

	}

	public function servicios(){


		return $this->belongsToMany('App\Servicio', 'huesped_reserva_servicio')
			->withPivot('id','reserva_id','cantidad', 'precio_total', 'estado')
			->withTimestamps();

	}

	public function calificacionPropiedades(){

        return $this->belongsToMany('App\Propiedad', 'huesped_propiedad')
        ->withPivot('comentario', 'calificacion')
        ->withTimestamps();


    }




}
