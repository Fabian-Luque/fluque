<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Response;

class MotorController extends Controller {
	public function getMotor(Request $request) {
		return "<h1>Motor de reserva</h1> 
<p>{{nombre}}</p>
<section layout='row' layout-sm='column' layout-align='center center' layout-wrap>
	<md-button class='md-raised'>Button</md-button>
	<md-button class='md-raised md-primary'>Primary</md-button>
	<md-button ng-disabled='true' class='md-raised md-primary'>Disabled</md-button>
	<md-button class='md-raised md-warn'>Warn</md-button>
	<div class='label'>Raised</div>
</section>";
	}
}



