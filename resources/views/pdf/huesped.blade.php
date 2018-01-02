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
        <h2 class="titulo">Huéspedes del {{ $fecha }}</h2>


        <div class="detalles" style="margin:20px 0px;">
          <table class="tabla-detalles">
            <tr>
              <th class="data-tabla-detalles borde-derecha" style="text-align:left;"><p>Habitación</p></th>
              <th class="data-tabla-detalles borde-derecha" style="text-align:left;"><p>Huéspedes</p></th>
              <th class="data-tabla-detalles borde-derecha" style="text-align:left;"><p>Check out</p></th>
              <th class="data-tabla-detalles borde-derecha" style="text-align:left;"><p>Estado</p></th>
            </tr>

             @foreach($habitaciones as $habitacion)

            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>{{ $habitacion->nombre }}</p></td>
              
              <td class="data-tabla-detalles borde-derecha">
                
                @foreach($habitacion['reservas'] as $reserva)

                  @foreach($reserva['huespedes'] as $huesped)

                    <p>{{ $huesped->nombre }} {{ $huesped->apellido }}</p>

                  @endforeach

                @endforeach
                
              </td>


              <td class="data-tabla-detalles borde-derecha">


                @foreach($habitacion['reservas'] as $reserva)

                    <p>{{ $reserva->checkout->format('d-m-Y') }}</p>
                   
                @endforeach

              

              </td>
              <td class="data-tabla-detalles borde-derecha"><p>{{ $habitacion->estado }}</p></td>


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