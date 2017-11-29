<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PropiedadTipoDeposito extends Model
{
    protected $table = 'propiedad_tipo_deposito';

	protected $fillable = ['valor', 'propiedad_id', 'tipo_deposito_id'];


    public function propiedad() {
        return $this->belongsTo('App\Propiedad', 'propiedad_id'); 
    }

    public function tipoDeposito() {
        return $this->belongsTo('App\TipoDeposito', 'tipo_deposito_id'); 
    }

}
