
<style media="screen">
	.contenedor-motor-reserva {
		height: 200px;
		background-color: #d1d1d1;
	}
</style>


<div class="contenedor-motor-reserva" layout="row" layout-align="center center" ng-cloak>
	<form name="motorReservaForm">
		<md-datepicker name="checkin" ng-model="check_in" md-placeholder="Check in" required></md-datepicker>
		<md-datepicker name="checkout" ng-model="check_out" md-placeholder="Check out" required></md-datepicker>
	</form>

	<md-button class="md-raised md-primary" ng-click="buscarDisponibilidad(motorReservaForm);" ng-disabled="motorReservaForm.$invalid">{{buscar}}</md-button>
</div>
