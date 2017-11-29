(function(){

  //Obtiene los parametros entregdos a traves de la ruta del widget. ""ruta?user=10"
  window.widgetParameters = getWidgetParameters();

  /*
  * Instalacion de librerias
  * AngularJS
  *
  * AngularMaterial
  */

  var angular_js_cargado = false;

  //Verifica si angular esta iniciado en la página
  if(typeof window.angular === 'undefined'){
    console.log('No esta cargado AngularJS');
      var angularJS = getAngularJS();
      document.getElementsByTagName("body")[0].appendChild(angularJS);

      if(angularJS.complete){
          document.write = document._write;
      }else{
          angularJS.onload = function(){
              setTimeout(function(){
                  document.write = document._write;
              }, 0);
              angular_js_cargado = true;
              verificarAngularAnimate();
          }
      }
  }else{
    console.log("AngularJS esta cargado");
    angular_js_cargado = true;
    verificarAngularAnimate();
  }






  function verificarAngularAnimate() {
    if(angular_js_cargado){
        var angularAnimate = getAngularAnimate();
        document.getElementsByTagName("body")[0].appendChild(angularAnimate);

        if(angularAnimate.complete){
            document.write = document._write;
        }else{
            angularAnimate.onload = function(){
                setTimeout(function(){
                    document.write = document._write;
                }, 0);
                verificarAngularAria();
            }
        }
    }else{
        verificarAngularAria();
    }
  }

  function verificarAngularAria() {
    if(angular_js_cargado){
        var angularAria = getAngularAria();
        document.getElementsByTagName("body")[0].appendChild(angularAria);

        if(angularAria.complete){
            document.write = document._write;
        }else{
            angularAria.onload = function(){
                setTimeout(function(){
                    document.write = document._write;
                }, 0);
                verificarAngularMessages();
            }
        }
    }else{
        verificarAngularMessages();
    }
  }

  function verificarAngularMessages() {
    if(angular_js_cargado){
        var angularMessages = getAngularMessages();
        document.getElementsByTagName("body")[0].appendChild(angularMessages);

        if(angularMessages.complete){
            document.write = document._write;
        }else{
            angularMessages.onload = function(){
                setTimeout(function(){
                    document.write = document._write;
                }, 0);
                verificarAngularMaterial();
            }
        }
    }else{
        verificarAngularMaterial();
    }
  }



  function verificarAngularMaterial() {
    if(typeof window.ngMaterial === 'undefined'){
      console.log('No esta cargado Material');
        var angularMaterial = getAngularMaterial();
        document.getElementsByTagName("body")[0].appendChild(angularMaterial);

        if(angularMaterial.complete){
            document.write = document._write;
        }else{
            angularMaterial.onload = function(){
                setTimeout(function(){
                    document.write = document._write;
                }, 0);
                console.log(window.ngMaterial);
                estilosAngularMaterial();
            }
        }
    }else{
      console.log("Material esta cargado");
        estilosAngularMaterial();
    }
  }

  function estilosAngularMaterial() {
    if(angular_js_cargado){
        var angulaEstilosrMaterial = getEstilosMaterial();
        document.getElementsByTagName("head")[0].appendChild(angulaEstilosrMaterial);

        if(angulaEstilosrMaterial.complete){
            document.write = document._write;
        }else{
            angulaEstilosrMaterial.onload = function(){
                setTimeout(function(){
                    document.write = document._write;
                }, 0);
                main();
            }
        }
    }else{
        main();
    }
  }









  /*
  * Obtiene los parametros de la ruta donde se buscan los archivos del widget
  * http://findeck.es/widgetGithubReposLoader.js?user=zamarrowski
  * Parametro: user = zamarrowki
  */
  function getWidgetParameters() {
      var url = document.currentScript.src;
      var urlSplit = url.split("?");
      var parameters = urlSplit[1] ? urlSplit[1] : null;
      parameters = parameters.split("&");
      var objectListParameters = [];
      if(parameters)
      for (var i = 0; i < parameters.length; i++) {
          var splitParam = parameters[i].split("=");
          var parameter = {
              name: splitParam[0],
              value: splitParam[1]
          };
          objectListParameters.push(parameter);
      }
      return objectListParameters;
  }

  //Carga la libreria de AngularJS
  function getAngularJS() {
      var script = document.createElement("script");
      script.type = "text/javascript";
      script.src = "https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular.min.js";
      return script;
  }

  function getAngularMaterial() {
      var script = document.createElement("script");
      script.src = "https://ajax.googleapis.com/ajax/libs/angular_material/1.1.4/angular-material.min.js";
      return script;
  }

  function getAngularAnimate() {
      var script = document.createElement("script");
      script.src = "https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular-animate.min.js";
      return script;
  }

  function getAngularAria() {
      var script = document.createElement("script");
      script.src = "https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular-aria.min.js";
      return script;
  }

  function getAngularMessages() {
      var script = document.createElement("script");
      script.src = "https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular-messages.min.js";
      return script;
  }

  function getEstilosMaterial() {
      var link = document.createElement("link");
      link.rel  = 'stylesheet';
      link.href = "https://ajax.googleapis.com/ajax/libs/angular_material/1.1.0/angular-material.min.css";
      console.log(link);
      return link;
  }




  //Busca el controller del widget y crea el elemento script
  function main() {
      buildWidgetHtml(function(){
          var widgetJS = document.createElement("script");
          widgetJS.type = "text/javascript";
          // widgetJS.src = "http://findeck.es/widgetGithubRepos.js"; //widgetURL
          widgetJS.src = "http://localhost:8000/motor/controlador.js"; //widgetURL
          (document.getElementsByTagName("body")[0] || document.documentElement).appendChild(widgetJS);
      });
  }

  //Crea el div con la id del widget "widget-gofeels-mr"
  //Añade como atributo el controller al div.
  function buildWidgetHtml(callback) {
      var widgetContainer = document.getElementById("widget-gofeels-mr");
      var appDiv = document.createElement("div");
      appDiv.setAttribute("ng-controller", "MotorReservaCtrl");
      appDiv.setAttribute("id", "MotorReservaCtrl");
      widgetContainer.appendChild(appDiv);
      loadTemplate(callback);
  }


  //Carga la vista al div creado con id "MotorReservaCtrl"
  function loadTemplate(callback) {
    var ajax = new XMLHttpRequest();
    ajax.open("GET","http://localhost:8000/motor/reserva");
    ajax.send();
    ajax.onreadystatechange=function(){
  		if(ajax.readyState == 4 && ajax.status == 200){
  			var response = ajax.responseText;
  			// document.getElementById("MotorReservaCtrl").innerHTML = response;
        document.getElementById("MotorReservaCtrl").innerHTML = response;

        // "<h1>Motor de reserva</h1> <p>{{nombre}}</p> <section layout='row' layout-sm='column' layout-align='center center' layout-wrap><md-button class='md-raised'>Button</md-button><md-button class='md-raised md-primary'>Primary</md-button><md-button ng-disabled='true' class='md-raised md-primary'>Disabled</md-button><md-button class='md-raised md-warn'>Warn</md-button><div class='label'>Raised</div></section>";

        callback();
  		}
    };
  }



})();
