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



          <h2 class="titulo">Entradas del día {{ $fecha }}</h2>




        <div class="detalles" style="margin:20px 0px;">
          <table class="tabla-detalles">



            <tr>
              <th class="data-tabla-detalles borde-derecha"><p>Nº Reserva</p></th>
              <th class="data-tabla-detalles borde-derecha"><p>Huésped</p></th>
              <th class="data-tabla-detalles borde-derecha"><p>Check in</p></th>
              <th class="data-tabla-detalles borde-derecha"><p>Check out</p></th>
              <th class="data-tabla-detalles borde-derecha"><p>Habitación/Tipo</p></th>
              <th class="data-tabla-detalles borde-derecha"><p>Cliente</p></th>
              <th class="data-tabla-detalles borde-derecha"><p>Monto total</p></th>
              <th class="data-tabla-detalles borde-derecha"><p>Estado</p></th>
            </tr>

         @foreach($reservas as $reserva)
            <tr>
              <td class="data-tabla-detalles borde-derecha" style="text-align:center;">
                <p>{{ $reserva['numero_reserva'] }}</p>
              </td>
              <td class="data-tabla-detalles borde-derecha">

              @foreach($reserva['huespedes'] as $huesped)
                <p>-{{ $huesped['nombre'] }} {{ $huesped['apellido'] }}</p>

              @endforeach

              </td>
              <td class="data-tabla-detalles borde-derecha" style="text-align:center;">
                <p>{{ $reserva['checkin']->format('d-m-Y') }}</p>
              </td>
              <td class="data-tabla-detalles borde-derecha" style="text-align:center;">
                <p>{{ $reserva['checkout']->format('d-m-Y') }}</p>
              </td>
              <td class="data-tabla-detalles borde-derecha" style="text-align:center;">


                <p>{{ $reserva['habitacion']['nombre']}}/{{ $reserva['habitacion']['tipoHabitacion']['nombre'] }}</p>


              </td>
              <td class="data-tabla-detalles borde-derecha" style="text-align:center;">
                <p>{{ $reserva['cliente']['nombre'] }} {{ $reserva['cliente']['apellido'] }}</p>
              </td>
              <td class="data-tabla-detalles borde-derecha" style="text-align:center;">
                <p>{{ $reserva['tipoMoneda']['nombre'] }} {{ $reserva['monto_alojamiento'] }}</p>
              </td>
              <td class="data-tabla-detalles borde-derecha" style="text-align:center;">
                <p>{{ $reserva['estadoReserva']['nombre'] }}</p>
              </td>
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
