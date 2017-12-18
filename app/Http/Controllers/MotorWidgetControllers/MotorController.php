<?php

namespace App\Http\Controllers\MotorWidgetControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Response;

class MotorController extends Controller {
	public function getMotor(Request $request) {
		return '
		<style media="screen">
			.contenedor-motor-reserva {
				height: 90px;
				background-color: transparent;
			}
			.md-datepicker-input-mask {
				overflow: hidden;
			}
			.contenedor-motor-reserva .mdr-btn {
				font-size: 13px;
			}
		</style>


		<div class="contenedor-motor-reserva" layout="row" layout-align="center center" md-theme="mdrTheme" ng-cloak>
			<form name="motorReservaForm">
				<md-datepicker name="checkin" ng-model="check_in" md-placeholder="Check in" md-min-date="minDate" ng-change="cambioCheckIn()" required style="width:100px;"></md-datepicker>
				<md-datepicker name="checkout" ng-model="check_out" md-placeholder="Check out" md-min-date="fecha_limite_2" required></md-datepicker>
			</form>

			<md-button class="md-raised md-accent mdr-btn" ng-click="buscarDisponibilidad(motorReservaForm);" ui-sref="asd" ng-disabled="motorReservaForm.$invalid">buscar</md-button>

		</div>
';
	}


}