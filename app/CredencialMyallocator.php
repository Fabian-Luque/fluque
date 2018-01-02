<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CredencialMyallocator extends Model {
    protected $table = 'credenciales_myallocator';

    public function propiedad() {
  		return $this->hasOne('App\Propiedad', 'prop_id');
	}
}