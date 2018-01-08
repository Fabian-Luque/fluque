<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoHabitacion extends Model
{
    
	protected $table = 'tipo_habitacion';

	protected $fillable = ['nombre', 'capacidad', 'cantidad', 'disponible_venta', 'venta_propiedad', 'propiedad_id'];


	public function habitaciones(){
		return $this->hasMany('App\Habitacion', 'tipo_habitacion_id');
	}

	public function precios(){
		return $this->hasMany('App\PrecioTemporada', 'tipo_habitacion_id');
	}

	public function propiedad(){
        return $this->belongsTo('App\Propiedad', 'propiedad_id'); 
    }

    public function reservas(){
		return $this->hasMany('App\Reserva', 'tipo_habitacion_id');
	}


}
