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
        <h2 class="titulo">Huéspedes del {{ $fecha }}</h2>


        <div class="detalles" style="margin:20px 0px;">
          <table class="tabla-detalles">
            <tr>
              <th class="data-tabla-detalles borde-derecha" style="text-align:left;"><p>Habitación</p></th>
              <th class="data-tabla-detalles borde-derecha" style="text-align:left;"><p>Huéspedes</p></th>
            </tr>

             @foreach($habitaciones as $habitacion)
              @foreach($habitacion['reservas'] as $reserva)
             
            <tr>
              <td class="data-tabla-detalles borde-derecha"><p>{{ $habitacion->nombre }}</p></td>
              
              <td class="data-tabla-detalles borde-derecha">
                  @foreach($reserva['huespedes'] as $huesped)
                  <p>{{ $huesped->nombre }} {{ $huesped->apellido }}</p>
                  @endforeach
              </td>
            </tr>
              @endforeach
               
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