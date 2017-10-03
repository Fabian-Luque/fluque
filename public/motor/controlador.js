(function(){
  'use strict';

  angular

    .module('motorReservaApp', [
      'ngMaterial',
    ])


    .controller('MotorReservaCtrl', mainCtrl);
    mainCtrl.$inject = ['$scope', '$http'];
    function mainCtrl($scope, $http){

        /* Variables del widget */
        // var parameters = window.widgetParameters;

        $scope.nombre = "Wicho";
        // $scope.fecha  = moment().format('LL');



    }

  angular.element(document).ready(function() {
      var divWidget = document.getElementById("widget-gofeels-mr");
      angular.bootstrap(divWidget, ['motorReservaApp']);
  });



})();