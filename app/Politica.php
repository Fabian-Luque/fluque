<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Politica extends Model
{
    protected $table = 'politicas';

	protected $fillable = ['descripcion'];

    public function propiedad() {
        return $this->belongsTo('App\Propiedad', 'propiedad_id'); 
    }
    
}
