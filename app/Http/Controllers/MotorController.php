<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Html;

use Response;


//use App\User;   


class MotorController extends Controller {

	public function getInicio() {
		return (url('motor/inicio.js'));
	}
}