<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PagoFacil extends Model {
    protected $table = 'pago_facil';
    protected $fillable = [ 
    	'id', 
    	'order_id',
    	'monto',
    	'email',
    	'status',
    	'pago_id',
    	'created_at',
    	'updated_at'
    ];
        
    public function pagoOnline() {
        return $this->hasOne('App\PagoOnline', 'pago_id');
    }
}