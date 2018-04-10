<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PagoOnline extends Model {
    protected $table = 'pagos_online';
    protected $fillable = [ 
    	'id', 
    	'estado',
    	'fecha_facturacion',
    	'pas_pago',
    	'prop_id',
    	'plan_id',
    	'created_at',
    	'updated_at'
    ];

    public function planes() {
        return $this->hasOne('App\Plan');
    }

    public function propiedades() {
        return $this->belongsToMany(
        	'App\Propiedad', 
        	'prop_id'
        );
    }
}