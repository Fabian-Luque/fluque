<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Caja;
use JWTAuth;
use \Carbon\Carbon;



class CajaController extends Controller
{
    
	public function abrirCaja(Request $request)
	{
		$user  = JWTAuth::parseToken()->toUser();

			









	}




}
