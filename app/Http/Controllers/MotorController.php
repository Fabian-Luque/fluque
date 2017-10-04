<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Response;

class MotorController extends Controller {
	public function getMotor(Request $request) {
		return '
		<style media="screen">
			.contenedor-motor-reserva {
				height: 200px;
				background-color: #d1d1d1;
			}
			.md-datepicker-input-mask {
				overflow: hidden;
			}
		</style>


		<div class="contenedor-motor-reserva" layout="row" layout-align="center center" ng-cloak>
			<form name="motorReservaForm">
				<md-datepicker name="checkin" ng-model="check_in" md-placeholder="Check in" required style="width:100px;"></md-datepicker>
				<md-datepicker name="checkout" ng-model="check_out" md-placeholder="Check out" required></md-datepicker>
			</form>

			<md-button class="md-raised md-primary" ng-click="buscarDisponibilidad(motorReservaForm);" ng-disabled="motorReservaForm.$invalid">{{buscar}}</md-button>

		</div>
';
	}

	public function getDisponibilidad(Request $request) {
		return '
		<div class="contenedor-motor-reserva" layout="row" layout-align="center center" ng-cloak>

		</div>
';
	}


}
