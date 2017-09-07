<!DOCTYPE html>
<html>
  <head>
    <title></title>
    <link rel="stylesheet" type="text/css" href="css/app.css">
  </head>

  <body>


    <div class="contenedor">
      <div class="encabezado">
        <div class="propiedad-info">

        @foreach($propiedad as $prop)
          <h2>{{$prop->nombre}}</h2>
          <h3>{{$prop->email}}</h3>
          <h3>{{$prop->direccion}}</h3>
          <h3>{{$prop->ciudad}}</h3>
          <h3>{{$prop->pais->nombre}}</h3>
        @endforeach
        </div>
      </div>

      <div class="estado-cuenta" style="margin-top: 20px;">



          <h2 class="titulo">Resumen financiero del mes de {{ $mes }}</h2>




        <div class="detalles" style="margin:20px 0px;">
          <table class="tabla-detalles">




            <tr>
              <th class="head-tabla-cliente"><p class="align-left">Ingreso por habitación</p></th>
          @foreach($ingresos['ingresos_por_habitacion'] as $ingreso)
              <td class="data-tabla-cliente"><p class="nombre">{{ $ingreso['nombre_moneda'] }} ${{$ingreso['monto'] }}</p></td>
          @endforeach
            </tr>



            <tr>
              <th class="head-tabla-cliente"><p class="align-left">Ingreso por consumos</p></th>
          @foreach($ingresos['ingresos_por_consumos'] as $ingreso)
              <td class="data-tabla-cliente"><p class="nombre">{{ $ingreso['nombre_moneda'] }} ${{$ingreso['monto'] }}</p></td>
          @endforeach 
            
            </tr>

            <tr>
              <th class="head-tabla-cliente"><p class="align-left">Total</p></th>
          @foreach($ingresos['ingresos_totales'] as $ingreso )

              <td class="data-tabla-cliente"><p class="nombre">{{ $ingreso['nombre_moneda'] }} ${{$ingreso['monto'] }}</p></td>
              
          @endforeach 

            </tr>
          </table>
        </div>




        <div class="detalles" style="margin:20px 0px;">
          <table class="tabla-detalles">




            <tr>
              <th class="data-tabla-detalles borde-derecha" style="width:300px;"><p>Ingreso por tipo de cliente</p></th>

            @foreach($propiedad as $prop)
              @foreach($prop->tipoMonedas as $moneda)



              <th class="data-tabla-detalles borde-derecha"><p> Ingresos ({{$moneda['nombre']}}) </p></th>


              @endforeach
            @endforeach
            </tr>



            @foreach($ingresos['tipos_clientes'] as $tipo)
            <tr>

              <td class="data-tabla-detalles borde-derecha"><p>{{ $tipo['nombre'] }}</p></td>

              @foreach($tipo['ingresos'] as $ingreso)
              <td class="data-tabla-detalles-nombre borde-derecha"><p>{{ $ingreso['nombre_moneda'] }} ${{ $ingreso['monto'] }}</p></td>

              @endforeach


            </tr>

            @endforeach

          </table>
        </div>

        <div class="detalles" style="margin:20px 0px;">
          <table class="tabla-detalles">
            <tr>
              <th class="data-tabla-detalles borde-derecha" style="width:300px;"><p>Ingreso por método de pago</p></th>

              @foreach($propiedad as $prop)
              @foreach($prop->tipoMonedas as $moneda)



              <th class="data-tabla-detalles borde-derecha"><p> Ingresos ({{$moneda['nombre']}}) </p></th>


              @endforeach
            @endforeach

            </tr>


            @foreach($ingresos['metodos_pagos'] as $metodo)

            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>{{ $metodo['nombre'] }}</p></td>

              @foreach($tipo['ingresos'] as $ingreso)
               <td class="data-tabla-detalles-nombre borde-derecha"><p>{{ $ingreso['nombre_moneda'] }} ${{ $ingreso['monto'] }}</p></td>
              
              @endforeach

            </tr>

            @endforeach

          </table>
        </div>


        <div class="detalles" style="margin:20px 0px;">
          <table class="tabla-detalles">
            <tr>
              <th class="data-tabla-detalles borde-derecha" style="width:300px;"><p>Ingreso por tipo de fuente</p></th>
              <th class="data-tabla-detalles borde-derecha"><p>Reservas</p></th>
              @foreach($propiedad as $prop)
              @foreach($prop->tipoMonedas as $moneda)

              <th class="data-tabla-detalles borde-derecha"><p> Ingresos ({{$moneda['nombre']}}) </p></th>

              @endforeach
            @endforeach
            </tr>


            @foreach($ingresos['tipos_fuentes'] as $tipo)
            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>{{ $tipo['nombre']  }}</p></td>
              <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p> {{ $tipo['cantidad'] }} </p></td>

              @foreach($tipo['ingresos'] as $ingreso)


              <td class="data-tabla-detalles-nombre borde-derecha"><p>{{ $ingreso['nombre_moneda'] }} ${{ $ingreso['monto'] }}</p></td>

              @endforeach
              
            </tr>
            @endforeach
          </table>
        </div>


        <div class="detalles" style="margin:20px 0px;">
          <table class="tabla-detalles">
            <tr>
              <th class="data-tabla-detalles borde-derecha" style="width:300px;"><p>Ingreso por tipo de habitaciones</p></th>
              <th class="data-tabla-detalles borde-derecha"><p>Cantidad</p></th>
              @foreach($propiedad as $prop)
              @foreach($prop->tipoMonedas as $moneda)

              <th class="data-tabla-detalles borde-derecha"><p> Ingresos ({{$moneda['nombre']}}) </p></th>

              @endforeach
            @endforeach
              
            </tr>


            @foreach($ingresos['tipos_habitaciones'] as $tipo)
            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>{{ $tipo['nombre']  }}</p></td>
              <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p> {{ $tipo['cantidad'] }} </p></td>

              @foreach($tipo['ingresos'] as $ingreso)


              <td class="data-tabla-detalles-nombre borde-derecha"><p>{{ $ingreso['nombre_moneda'] }} ${{ $ingreso['monto'] }}</p></td>

              @endforeach
              
            </tr>
            @endforeach

          </table>
        </div>



        <div class="detalles" style="margin:20px 0px;">
          <table class="tabla-detalles">



            <tr>
              <th class="data-tabla-detalles borde-derecha" style="width:300px;"><p>Consumos de productos y servicios</p></th>
              <th class="data-tabla-detalles borde-derecha"><p>Cantidad</p></th>
            </tr>

            @foreach($ingresos['cantidad_servicios'] as $servicio)
            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>{{ $servicio['nombre'] }}</p></td>
              <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p>{{ $servicio['cantidad'] }}</p></td>
            </tr>
            @endforeach

          </table>
        </div>




      </div>
      <!--  Fin estado-cuenta  -->
    </div>
    <!-- Fin contenedor -->

    <div class="footer">
      <p>Documento generado con Jarvis Frontdesk</p>
    </div>







  </body>
</html>