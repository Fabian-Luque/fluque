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