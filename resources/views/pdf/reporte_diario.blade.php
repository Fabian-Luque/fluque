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


      

        @if($fechas['inicio'] == $fechas['fin'])


          <h2 class="titulo">Resumen del día {{ $fechas['inicio'] }}</h2>


        @else

          <h2 class="titulo">Resumen comprendido entre el {{ $fechas['inicio'] }} y {{ $fechas['fin'] }}</h2>

        @endif



        <div class="detalles" style="margin:20px 0px;">
          <table class="tabla-detalles">
            <tr>
              <th class="head-tabla-cliente"><p class="align-left">Reservas realizadas</p></th>
              <td class="data-tabla-cliente"><p class="nombre">{{ $reservas_realizadas }}</p></td>
            </tr>
            <tr>
              <th class="head-tabla-cliente"><p class="align-left">Reservas anuladas</p></th>
              <td class="data-tabla-cliente"><p class="nombre">{{ $reservas_anuladas }}</p></td>
            </tr>
            <tr>
              <th class="head-tabla-cliente"><p class="align-left">No show</p></th>
              <td class="data-tabla-cliente"><p class="nombre">{{ $reservas_no_show }}</p></td>
            </tr>
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

<!-- INICIO TABLA DE OCUPACION -->

        <div class="titulo" style="text-align:left;">
          <h3 class="">Ocupación</h3>
        </div>

        <div class="detalles" style="margin:20px 0px;">
          <table class="tabla-detalles">
            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>Ocupado</p></td>

              <td class="data-tabla-detalles borde-derecha"><p>{{ $ocupado }}</p></td>

            </tr>
            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>Disponible</p></td>

              <td class="data-tabla-detalles borde-derecha"><p>{{ $disponible }}</p></td>

            </tr>
          </table>
        </div>

<!-- FIN TABLA DE OCUPACION -->







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

        <!-- INICIO TABLA HUESPEDES LOCALES -->

                <div class="titulo" style="text-align:left;">
                  <h3 class="">Huéspedes locales</h3>
                </div>

                <div class="detalles" style="margin:20px 0px;">
                  <table class="tabla-detalles">
                    <tr>
                      <th class="data-tabla-detalles borde-derecha" style="width:300px;"><p>Región</p></th>
                      <th class="data-tabla-detalles borde-derecha"><p>Llegadas</p></th>
                      <th class="data-tabla-detalles borde-derecha"><p>Pernoctación</p></th>
                    </tr>

                    @foreach($residentes_locales as $local)
                    <tr>
                      <td class="data-tabla-detalles borde-derecha"><p>{{ $local['nombre'] }}</p></td>
                      <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p>{{ $local['llegadas'] }}</p></td>
                      <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p>{{ $local['pernoctacion'] }}</p></td>
                    </tr>
                    @endforeach


                  </table>
                </div>

        <!-- FIN TABLA HUESPEDES LOCALES -->


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

        <!-- INICIO TABLA HUESPEDES EXTRANJEROS -->

                <div class="titulo" style="text-align:left;">
                  <h3 class="">Huéspedes extranjeros</h3>
                </div>

                <div class="detalles" style="margin:20px 0px;">
                  <table class="tabla-detalles">
                    <tr>
                      <th class="data-tabla-detalles borde-derecha" style="width:300px;"><p>País</p></th>
                      <th class="data-tabla-detalles borde-derecha"><p>Llegadas</p></th>
                      <th class="data-tabla-detalles borde-derecha"><p>Pernoctación</p></th>
                    </tr>

                    @foreach($residentes_extranjero as $pais)
                    <tr>
                      <td class="data-tabla-detalles borde-derecha"><p>{{ $pais['nombre'] }}</p></td>
                      <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p>{{ $pais['llegadas'] }}</p></td>
                      <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p>{{ $pais['pernoctacion'] }}</p></td>
                    </tr>
                    @endforeach

                  </table>
                </div>

        <!-- FIN TABLA HUESPEDES EXTRANJEROS -->


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