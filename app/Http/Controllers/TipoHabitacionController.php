<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\TipoHabitacion;

class TipoHabitacionController extends Controller
{
    

	public function index(){

		$tipoHabitacion = TipoHabitacion::all();
			return $tipoHabitacion;

	}





}
