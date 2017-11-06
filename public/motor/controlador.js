(function(){
  'use strict';

  angular

    .module('motorReservaApp', [
      'ngMaterial',
    ])


    .controller('MotorReservaCtrl', mainCtrl);
    mainCtrl.$inject = ['$scope', '$http', '$mdDialog'];
    function mainCtrl($scope, $http, $mdDialog){

        /* Variables del widget */
        // var parameters = window.widgetParameters;

        $scope.buscar = "buscar";
        $scope.check_in = new Date();
        $scope.check_out = new Date();

        // $scope.fecha  = moment().format('LL');

        $scope.buscarDisponibilidad = function(form) {
          if (form.$valid) {
            console.log("Formulario completado correctamente... Buscando disponibilidad");
            nuevaHabitacion();
          }
        };


        var buscarTmplDisponibilidad = function() {
          $http.get("http://localhost:8000/motor/reserva")
          .then(function(response){
            console.log(response.data);

            return '<md-dialog aria-label="disponibilidad">' +
            '<p>Disponibilidad</p>'+
            '</md-dialog>';
          })
        };
        var asd = buscarTmplDisponibilidad();




        /* DIALOG */
        var CtrlDisponibilidad = ["$mdDialog", "$http",
          function ($mdDialog, $http) {
            var vm = this;

            console.log("Controladooooor");

            vm.hide = function() {
              $mdDialog.hide();
            };
            vm.cancel = function() {
              $mdDialog.cancel();
            };
            vm.answer = function(answer) {
              $mdDialog.hide(answer);
            };

          }];


        var nuevaHabitacion = function() {
            $mdDialog.show({
              controller: CtrlDisponibilidad,
              controllerAs: 'dnh',
              template: '<md-dialog aria-label="disponibilidad" style="width:100%;height:100%;">' +
              '<p>Disponibilidad</p>'+
              '<md-datepicker name="checkout" ng-model="check_out" md-placeholder="Check out" required></md-datepicker>'+
              '</md-dialog>',
              parent: angular.element(document.body),
              // targetEvent: ev,
              clickOutsideToClose:true,
              bindToController: true,
              locals: {
                // tipos_habitaciones: vm.tipos_habitaciones
              }
            })
            .then(function(response){
              if (response) {
                vm.obtenerHabitaciones();
              }
            })
          };


    }

  angular.element(document).ready(function() {
      var divWidget = document.getElementById("widget-gofeels-mr");
      angular.bootstrap(divWidget, ['motorReservaApp']);
  });



})();
