<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
 
class PagoOnline extends Model {
    protected $table = 'pagos_online';
    protected $fillable = [ 
    	'id', 
    	'estado',
    	'fecha_facturacion',
        'prox_fac',
    	'pas_pago_id',
    	'prop_id',
    	'plan_id',
    	'created_at',
    	'updated_at'
    ];

    public function planes() {
        return $this->hasOne('App\Plan', 'plan_id');
    }

    public function pas_pago() {
        return $this->hasOne('App\PasPago', 'pas_pago_id');
    }

    public function propiedades() {
        return $this->belongsToMany(
        	'App\Propiedad', 
        	'prop_id'
        );
    }
}