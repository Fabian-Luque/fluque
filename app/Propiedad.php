<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Propiedad extends Model {
	use SoftDeletes;
    protected $table = 'propiedades';

	protected $fillable = [ 'id', 'nombre','tipo', 'numero_habitaciones','region','pais','ciudad','estado','direccion', 'telefono', 'email', 'nombre_responsable', 'descripcion','iva', 'porcentaje_deposito', 'pais_id', 'estado_cuenta_id','region_id', 'tipo_propiedad_id', 'tipo_cobro_id', 'zona_horaria_id'];

    public function user() {
        return $this->belongsToMany(
            'App\User', 
            'propiedad_user',
            'propiedad_id',
            'user_id'
        ); 
    }

    public function estadocuenta() {
        return $this->hasOne(
            'App\Estadocuenta'
        );
    }

    public function pais() {
        return $this->belongsTo('App\Pais', 'pais_id'); 
    }

    public function region() {
        return $this->belongsTo('App\Region', 'region_id'); 
    }

    public function roles(){
        return $this->hasMany('App\Rol', 'propiedad_id');
    }

    public function tiposHabitacion(){
        return $this->hasMany('App\TipoHabitacion', 'propiedad_id');
    }

    public function zonaHoraria() {
        return $this->belongsTo('App\zonaHoraria', 'zona_horaria_id'); 
    }

    public function habitaciones() {
    	return $this->hasMany('App\Habitacion', 'propiedad_id');
    }

    public function servicios() {
    	return $this->hasMany('App\Servicio', 'propiedad_id');
    }

    public function tipoPropiedad() {
        return $this->belongsTo('App\TipoPropiedad', 'tipo_propiedad_id');
    }

    public function temporadas() {
        return $this->hasMany('App\Temporada', 'propiedad_id');
    }

    public function tipoCobro(){
        return $this->belongsTo('App\TipoCobro', 'tipo_cobro_id');
    }

    public function calificacionHuespedes() {
        return $this->belongsToMany('App\Huesped', 'huesped_propiedad')
        ->withPivot('comentario', 'calificacion')
        ->withTimestamps();
    }

    public function vendeServicios() {
        return $this->belongsToMany('App\Servicio', 'metodo_pago_propiedad_servicio')
            ->withPivot('metodo_pago_id', 'tipo_comprobate_id', 'cantidad', 'precio_total', 'numero_operacion')
            ->withTimestamps();
    }

    public function metodosPago() {
        return $this->belongsToMany('App\MetodoPago', 'metodo_pago_propiedad_servicio')
            ->withPivot('servicio_id', 'tipo_comprobate_id', 'cantidad', 'precio_total', 'numero_operacion')
            ->withTimestamps();
    }

    public function tiposComprobante() {
        return $this->belongsToMany('App\TipoComprobante', 'metodo_pago_propiedad_servicio')
            ->withPivot('servicio_id','metodo_pago_id' , 'cantidad', 'precio_total', 'numero_operacion')
            ->withTimestamps();
    }

    public function consumoServiciosClientes() {
        return $this->belongsToMany('App\Cliente', 'cliente_propiedad_servicio')
            ->withPivot('servicio_id', 'nombre_consumidor','apellido_consumidor' ,'rut_consumidor','cantidad', 'precio_total')
            ->withTimestamps();
    }

    public function consumoClienteServicios(){
        return $this->belongsToMany('App\Servicio', 'cliente_propiedad_servicio')
            ->withPivot('cliente_id', 'nombre_consumidor','apellido_consumidor' ,'rut_consumidor','cantidad', 'precio_total')
            ->withTimestamps();
    }

    public function tipoMonedas(){
        return $this->belongsToMany('App\TipoMoneda', 'propiedad_moneda')
            ->withPivot('id','clasificacion_moneda_id')
            ->withTimestamps();
    }

    public function clasificacionMonedas(){
        return $this->belongsToMany('App\ClasificacionMoneda', 'propiedad_moneda')
            ->withPivot('tipo_moneda_id')
            ->withTimestamps();
    }
}