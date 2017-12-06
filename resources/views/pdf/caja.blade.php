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
        <h2 class="titulo">Detalle de caja</h2>





        <div class="cliente">
                <table class="tabla-cliente">

                @foreach($detalle_caja as $caja)
                  
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Responsable</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $caja->user->name }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Fecha de apertura</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $caja->fecha_apertura }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Fecha de cierre</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $caja->fecha_cierre }}</p></td>
                    </tr>


                  
                @endforeach

                </table>
        </div>


        <div class="detalles" style="margin:20px 0px;">
          <table class="tabla-detalles">

            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>Ingreso</p></td>
          @foreach($monedas as $moneda)

              <td class="data-tabla-detalles borde-derecha"><p>{{ $moneda['nombre'] }} ${{ $moneda['ingreso'] }}</p></td>
          @endforeach
            </tr>

            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>Egreso</p></td>
          @foreach($monedas as $moneda)

              <td class="data-tabla-detalles borde-derecha"><p>{{ $moneda['nombre'] }} ${{ $moneda['egreso'] }}</p></td>
          @endforeach
            </tr>

            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>Saldo</p></td>
          @foreach($monedas as $moneda)

              <td class="data-tabla-detalles borde-derecha"><p>{{ $moneda['nombre'] }} ${{ $moneda['saldo'] }}</p></td>
          @endforeach
            </tr>




          </table>
        </div>

        <div class="titulo" style="text-align:left;">
          <h4 class="">Ingresos por métodos de pago</h4>
        </div>

        <div class="detalles" style="margin:20px 0px;">
          <table class="tabla-detalles">

          @foreach($metodos_pago as $metodo)

            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>{{ $metodo['nombre'] }}</p></td>

              @foreach($metodo['ingresos'] as $ingreso)
              <td class="data-tabla-detalles borde-derecha"><p>{{ $ingreso['nombre_moneda'] }} ${{ $ingreso['monto'] }}</p></td>
              @endforeach

            </tr>

          @endforeach


          </table>
        </div>


        <!-- inicio -->
          <div class="titulo" style="text-align:left;">
            <h3 class="">Detalle de ingresos</h3>
          </div>

          <div class="detalles" style="margin:20px 0px;">
            <table class="tabla-detalles">
              <tr>
               <th class="data-tabla-detalles borde-derecha"><p>Nº Reserva</p></th>
                <th class="data-tabla-detalles borde-derecha"><p>Tipo de pago</p></th>
                <th class="data-tabla-detalles borde-derecha"><p>Método de pago</p></th>
                <th class="data-tabla-detalles borde-derecha"><p>Tipo de comprobante</p></th>
                <th class="data-tabla-detalles borde-derecha"><p>Nº de operación</p></th>
                <th class="data-tabla-detalles borde-derecha"><p>Monto</p></th>
              </tr>

              @foreach($detalle_caja as $caja)
                @foreach($caja['pagos'] as $pago)
              <tr>

                <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p>{{ $pago->reserva->numero_reserva }}</p></td>
                <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p>{{ $pago->tipo }}</p></td>
                <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p>{{ $pago->metodoPago->nombre }}, {{ $pago->numero_cheque }}</p></td>
                <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p>{{ $pago['tipoComprobante']['nombre'] }}</p></td>
                <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p>{{ $pago->numero_operacion }}</p></td>
                <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p>{{ $pago->tipoMoneda->nombre }} {{ $pago->monto_equivalente }}</p></td>

              </tr>
                @endforeach
              @endforeach
            </table>
          </div>

          <div class="titulo" style="text-align:left;">
            <h3 class="">Detalle de egresos</h3>
          </div>

          <div class="detalles" style="margin:20px 0px;">
            <table class="tabla-detalles">
              <tr>
                <th class="data-tabla-detalles borde-derecha"><p>Tipo de egreso</p></th>
                <th class="data-tabla-detalles borde-derecha"><p>Descripcion</p></th>
                <th class="data-tabla-detalles borde-derecha"><p>Monto</p></th>
              </tr>


              @foreach($detalle_caja as $caja)
                @foreach($caja->egresosCaja as $egro)

              <tr>
                <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p>{{ $egro->egreso->nombre }}</p></td>
                <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p>{{ $egro->descripcion }}</p></td>
                <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p>{{ $egro->tipoMoneda->nombre }} {{ $egro->monto }}</p></td>
              </tr>

                @endforeach
              @endforeach

            </table>
          </div>
        <!-- FIN -->


      </div>
      <!--  Fin estado-cuenta  -->
    </div>



    <!-- Fin contenedor -->

    <div class="footer">
      <p>Documento generado con Jarvis Frontdesk</p>
    </div>


    <!-- <div class="page-break"></div> -->






  </body>
</html>