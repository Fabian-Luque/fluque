<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DatosStripe extends Model {
    
    protected $table = 'datos_stripe';

	protected $fillable = [ 
        'id', 
        'plan_id',
        'cliente_id',
        'subs_id'
    ];

    public function propiedad() {
        return $this->hasOne('App\Propiedad');
    }
}