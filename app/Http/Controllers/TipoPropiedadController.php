<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\TipoPropiedad;

class TipoPropiedadController extends Controller
{


	public function index(){
    
	$TipoPropiedad = TipoPropiedad::all();
		return $TipoPropiedad; 

	}

}
