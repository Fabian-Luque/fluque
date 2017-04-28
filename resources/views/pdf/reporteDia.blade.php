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
          <h3>{{$prop->pais}}</h3>
        @endforeach
        </div>
      </div>

      <div class="estado-cuenta" style="margin-top: 20px;">
        <h2 class="titulo">Resumen del día {{ $fecha }}</h2>





        

        <div class="detalles" style="margin:20px 0px;">
          <table class="tabla-detalles">
            <tr>
              <th class="head-tabla-cliente"><p class="align-left">Reservas realizadas</p></th>
              <td class="data-tabla-cliente"><p class="nombre">{{ $reservas_realizadas }}</p></td>
            </tr>
<!--             <tr>
              <th class="head-tabla-cliente"><p class="align-left">Reservas canceladas</p></th>
              <td class="data-tabla-cliente"><p class="nombre">0</p></td>
            </tr>
            <tr>
              <th class="head-tabla-cliente"><p class="align-left">No show</p></th>
              <td class="data-tabla-cliente"><p class="nombre">0</p></td>
            </tr> -->
          </table>
        </div>
       


        <div class="titulo" style="text-align:left;">
          <h3 class="">Ingresos totales</h3>
        </div>

        <div class="detalles" style="margin:20px 0px;">
          <table class="tabla-detalles">
            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>Ventas en habitaciones</p></td>

               @foreach($ingresos_habitacion as $ingreso)
                  @if($ingreso['tipo_moneda_id'] == 1)
              <td class="data-tabla-detalles borde-derecha"><p>CLP ${{ $ingreso['monto'] }}</p></td>
                  @else
              <td class="data-tabla-detalles borde-derecha"><p>USD ${{ $ingreso['monto'] }}</p></td>
                  @endif
     
               @endforeach

            </tr>
            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>Venta en servicios</p></td>
               @foreach($ingresos_consumo as $ingreso)
                  @if($ingreso['tipo_moneda_id'] == 1)
              <td class="data-tabla-detalles borde-derecha"><p>CLP ${{ $ingreso['monto'] }}</p></td>

                  @else
              <td class="data-tabla-detalles borde-derecha"><p>USD ${{ $ingreso['monto'] }}</p></td>
                  @endif
               @endforeach
            </tr>
            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>Total</p></td>
                @foreach($ingresos_totales as $ingreso)
                  @if($ingreso['tipo_moneda_id'] == 1)
              <td class="data-tabla-detalles borde-derecha"><p>CLP ${{ $ingreso['monto'] }}</p></td>
                  @else
              <td class="data-tabla-detalles borde-derecha"><p>USD ${{ $ingreso['monto'] }}</p></td>
                  @endif
               @endforeach
            </tr>
          </table>
        </div>



        <div class="titulo" style="text-align:left;">
          <h3 class="">Ingreso por tipo de pago</h3>
        </div>

        <div class="detalles" style="margin:20px 0px;">
          <table class="tabla-detalles">
            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>Efectivo</p></td>
                @foreach($ingresos_efectivo as $ingreso)
                  @if($ingreso['tipo_moneda_id'] == 1)
              <td class="data-tabla-detalles borde-derecha"><p>CLP ${{ $ingreso['monto'] }}</p></td>
                  @else
              <td class="data-tabla-detalles borde-derecha"><p>USD ${{ $ingreso['monto'] }}</p></td>
                  @endif
               @endforeach
            </tr>
            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>Credito</p></td>
                @foreach($ingresos_credito as $ingreso)
                  @if($ingreso['tipo_moneda_id'] == 1)
              <td class="data-tabla-detalles borde-derecha"><p>CLP ${{ $ingreso['monto'] }}</p></td>
                  @else
              <td class="data-tabla-detalles borde-derecha"><p>USD ${{ $ingreso['monto'] }}</p></td>
                  @endif
               @endforeach
            </tr>
            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>Debito</p></td>
                @foreach($ingresos_debito as $ingreso)
                  @if($ingreso['tipo_moneda_id'] == 1)
              <td class="data-tabla-detalles borde-derecha"><p>CLP ${{ $ingreso['monto'] }}</p></td>
                  @else
              <td class="data-tabla-detalles borde-derecha"><p>USD ${{ $ingreso['monto'] }}</p></td>
                  @endif
               @endforeach
            </tr>
            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>Cheque</p></td>
                @foreach($ingresos_cheque as $ingreso)
                  @if($ingreso['tipo_moneda_id'] == 1)
              <td class="data-tabla-detalles borde-derecha"><p>CLP ${{ $ingreso['monto'] }}</p></td>
                  @else
              <td class="data-tabla-detalles borde-derecha"><p>USD ${{ $ingreso['monto'] }}</p></td>
                  @endif
               @endforeach
            </tr>
            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>Tarjeta de crédito</p></td>
                @foreach($ingresos_tarjeta_credito as $ingreso)
                  @if($ingreso['tipo_moneda_id'] == 1)
              <td class="data-tabla-detalles borde-derecha"><p>CLP ${{ $ingreso['monto'] }}</p></td>
                  @else
              <td class="data-tabla-detalles borde-derecha"><p>USD ${{ $ingreso['monto'] }}</p></td>
                  @endif
               @endforeach
            </tr>
            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>Transferencia</p></td>
                @foreach($ingresos_transferencia as $ingreso)
                  @if($ingreso['tipo_moneda_id'] == 1)
              <td class="data-tabla-detalles borde-derecha"><p>CLP ${{ $ingreso['monto'] }}</p></td>
                  @else
              <td class="data-tabla-detalles borde-derecha"><p>USD ${{ $ingreso['monto'] }}</p></td>
                  @endif
               @endforeach
            </tr>
          </table>
        </div>




        <div class="titulo" style="text-align:left;">
          <h3 class="">Reservas por tipo de fuente</h3>
        </div>

        <div class="detalles" style="margin:20px 0px;">
          <table class="tabla-detalles">
            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>Página web</p></td>
              <td class="data-tabla-detalles borde-derecha"><p>{{ $pagina_web }}</p></td>
            </tr>
            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>Email</p></td>
              <td class="data-tabla-detalles borde-derecha"><p>{{ $email }}</p></td>
            </tr>
            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>Redes sociales</p></td>
              <td class="data-tabla-detalles borde-derecha"><p>{{ $redes_sociales }}</p></td>
            </tr>
            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>Caminando</p></td>
              <td class="data-tabla-detalles borde-derecha"><p>{{ $caminando }}</p></td>
            </tr>
            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>Telefono</p></td>
              <td class="data-tabla-detalles borde-derecha"><p>{{ $telefono }}</p></td>
            </tr>
            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>Expedia</p></td>
              <td class="data-tabla-detalles borde-derecha"><p>{{ $expedia }}</p></td>
            </tr>
            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>Booking.com</p></td>
              <td class="data-tabla-detalles borde-derecha"><p>{{ $booking }}</p></td>
            </tr>
            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>Airbnb</p></td>
              <td class="data-tabla-detalles borde-derecha"><p>{{ $airbnb }}</p></td>
            </tr>
          </table>
        </div>










      </div>
      <!--  Fin estado-cuenta  -->
    </div>
    <!-- Fin contenedor -->

    <div class="footer">
      <p>Documento generado con Jarvis Frontdesk</p>
    </div>


    <div class="page-break"></div>










    <div class="contenedor">
      <div class="estado-cuenta" style="margin-top: 20px;">

        <div class="titulo" style="text-align:left;">
          <h3 class="">Ingreso por tipo de cliente</h3>
        </div>

        <div class="detalles" style="margin:20px 0px;">
          <table class="tabla-detalles">
            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>Particular</p></td>
                @foreach($ingresos_particular as $ingreso)
                  @if($ingreso['tipo_moneda_id'] == 1)
              <td class="data-tabla-detalles borde-derecha"><p>CLP ${{ $ingreso['monto'] }}</p></td>
                  @else
              <td class="data-tabla-detalles borde-derecha"><p>USD ${{ $ingreso['monto'] }}</p></td>
                  @endif
               @endforeach
            </tr>
            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>Particular</p></td>
                @foreach($ingresos_empresa as $ingreso)
                  @if($ingreso['tipo_moneda_id'] == 1)
              <td class="data-tabla-detalles borde-derecha"><p>CLP ${{ $ingreso['monto'] }}</p></td>
                  @else
              <td class="data-tabla-detalles borde-derecha"><p>USD ${{ $ingreso['monto'] }}</p></td>
                  @endif
               @endforeach
            </tr>
<!--             <tr>
              <td class="data-tabla-detalles borde-derecha"><p>Gente de paso diario</p></td>
              <td class="data-tabla-detalles borde-derecha"><p>CLP 0</p></td>
              <td class="data-tabla-detalles borde-derecha"><p>USD 0.00</p></td>
            </tr> -->
          </table>
        </div>


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