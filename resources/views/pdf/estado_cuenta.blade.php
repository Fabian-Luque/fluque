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

      <div class="estado-cuenta">
        <h2 class="titulo">Estado de cuenta</h2>

        <div class="cliente">
                <table class="tabla-cliente">

                @foreach($cliente as $cte)

                    @if($cte->tipo_cliente_id == 1)
                      @if($cte->pais_id == null)

                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Nombre</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cte->nombre }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Apellido</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cte->apellido }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Rut</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cte->rut }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Dirección</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cte->direccion }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Ciudad</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cte->ciudad }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">País</p></th>
                        <td class="data-tabla-cliente"><p class="nombre"></p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Teléfono</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cte->telefono }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Email</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cte->email }}</p></td>
                    </tr>
                      @else
                      <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Nombre</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cte->nombre }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Apellido</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cte->apellido }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Rut</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cte->rut }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Dirección</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cte->direccion }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Ciudad</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cte->ciudad }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">País</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cte->pais->nombre }}</p><</td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Teléfono</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cte->telefono }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Email</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cte->email }}</p></td>
                    </tr>
                     @endif

                    @else
                      @if($cte->pais_id == null)

                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Razon social</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cte->nombre }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Rut</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cte->rut }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Giro</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cte->giro }}</p></td>
                    </tr>

                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Dirección</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cte->direccion }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Ciudad</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cte->ciudad }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">País</p></th>
                        <td class="data-tabla-cliente"><p class="nombre"></p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Teléfono</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cte->telefono }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Email</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cte->email }}</p></td>
                    </tr>
                      @else
                      <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Razon social</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cte->nombre }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Rut</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cte->rut }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Giro</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cte->giro }}</p></td>
                    </tr>

                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Dirección</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cte->direccion }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Ciudad</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cte->ciudad }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">País</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cte->pais->nombre }}</p><</td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Teléfono</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cte->telefono }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Email</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cte->email }}</p></td>
                    </tr>

                      @endif
                    @endif


                    @endforeach
                </table>
        </div>
        <!--  Fin cliente  -->
        <div class="detalles">
          <div class="titulo">
            <h3 class="">Detalles de las reservas/estadías</h3>
          </div>

          <table class="tabla-detalles">

          @foreach($reservas_pdf as $reservas)

            @foreach($reservas as $reserva)
            <tr>
              <td class="data-tabla-detalles borde-derecha"><p class="titulo">Habitacion {{ $reserva->habitacion->nombre }} - {{ $reserva->habitacion->tipoHabitacion->nombre }} - {{ $reserva->ocupacion }} Huéspedes - {{ $reserva->noches }} Noches - Checkin {{ $reserva->checkin }} - Checkout {{ $reserva->checkout }}</p></td>
              <td class="data-tabla-detalles-right align-right"><p class="nombre">{{ $reserva->tipoMoneda->nombre }} ${{ $reserva->monto_alojamiento }}</p></td>
            </tr>

            <tr>
              <td class="data-tabla-detalles borde-derecha"><p class="titulo">Consumos</p></td>
              <td class="data-tabla-detalles-right align-right"><p class="nombre">{{ $reserva->tipoMoneda->nombre }} ${{ $consumo }}</p></td>
            </tr>
            <tr>
              <th class="data-tabla-detalles borde-derecha"><p class="titulo align-right">Subtotal</p></th>
              <td class="data-tabla-detalles-right align-right"><p class="nombre">{{ $reserva->tipoMoneda->nombre }} ${{ $neto }}</p></td>
            </tr>
            <tr>
              <th class="data-tabla-detalles borde-derecha"><p class="titulo align-right">IVA</p></th>
              <td class="data-tabla-detalles-right align-right"><p class="nombre">{{ $reserva->tipoMoneda->nombre }} ${{ $iva }}</p></td>
            </tr>
            <tr>
              <th class="data-tabla-detalles borde-derecha"><p class="titulo align-right">Total</p></th>
              <td class="data-tabla-detalles-right align-right"><p class="nombre">{{ $reserva->tipoMoneda->nombre }} ${{ $total }}</p></td>
            </tr>

          </table>
        </div>
        <!--  Fin detalles  -->

            @endforeach
          @endforeach



      </div>
      <!--  Fin estado-cuenta  -->
    </div>
    <!-- Fin contenedor -->

    <div class="footer">
      <p>Documento generado con Jarvis Frontdesk</p>
    </div>

    <div class="firmas">
      <div class="seccion">
        <p class="firma">Firma de cliente</p>
      </div>

      <div class="seccion">
        <p class="firma">Firma Autorizada Recepción</p>
      </div>
    </div>



    <div class="page-break"></div>

    <div class="contenedor">

      <div class="detalle-consumo">
        <h2 class="titulo">Detalle de consumos</h2>
        @foreach($cliente as $cte)
        <h3 class="margen">Cliente principal de la reserva: <span>{{ $cte->nombre }} {{ $cte->apellido }}</span></h3>
        @endforeach

        <div class="">

        @foreach($reservas_pdf as $reservas)
          @foreach($reservas as $reserva)
            @foreach($reserva->huespedes as $huesped)
          <p class="negrita">{{ $huesped->nombre }} {{ $huesped->apellido }}</p>
          <p class="negrita">Habitacion {{ $reserva->habitacion->nombre }} - {{ $reserva->habitacion->tipoHabitacion->nombre }}</p>

          <table class="tabla-comsumos margen">
              @foreach($huesped->servicios as $servicio)
            <tr>
              <td class="data-tabla-detalles borde-derecha"><p class="">{{ $servicio->pivot->created_at->format('d-m-Y') }}   -  {{ $servicio->pivot->cantidad }} {{ $servicio->nombre }}</p></td>
              <td class="data-tabla-detalles-right align-right"><p class="nombre">{{ $reserva->tipoMoneda->nombre }} ${{ $servicio->pivot->precio_total }}</p></td>
            </tr>
              @endforeach
            <tr>
              <th class="data-tabla-detalles borde-derecha"><p class="titulo align-right">Total</p></th>
              <td class="data-tabla-detalles-right align-right"><p class="nombre">{{ $reserva->tipoMoneda->nombre }} ${{ $huesped->monto_consumo }}</p></td>
            </tr>
          </table>
            @endforeach
          @endforeach
        @endforeach
        </div>



      </div>


    </div>

    <div class="page-break"></div>

    <div class="contenedor">
      <div class="estado-cuenta" style="margin-top: 20px;">

                <div class="titulo" style="text-align:left;">
                  <h3 class="">Detalle de pagos</h3>
                </div>

                <div class="detalles" style="margin:20px 0px;">
                  <table class="tabla-detalles">
                    <tr>
                      <th class="data-tabla-detalles borde-derecha" style="width:150px;"><p>Tipo de pago</p></th>
                      <th class="data-tabla-detalles borde-derecha"><p>Fecha</p></th>
                      <th class="data-tabla-detalles borde-derecha"><p>Método de pago</p></th>
                      <th class="data-tabla-detalles borde-derecha"><p>Tipo de comprobante</p></th>
                      <th class="data-tabla-detalles borde-derecha"><p>Nº de operación</p></th>
                      <th class="data-tabla-detalles borde-derecha"><p>Monto</p></th>
                    </tr>

                @foreach($reservas_pdf as $reservas)
                    @foreach($reservas as $reserva)
                     @foreach($reserva->pagos as $pago)
                    <tr>
                      <td class="data-tabla-detalles borde-derecha"><p>{{ $pago->tipo }}</p></td>
                      <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p>{{ $pago->created_at }}</p></td>
                      <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p>{{ $pago->metodoPago->nombre }} {{$pago->numero_cheque }}</p></td>
                      <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p>{{ $pago->tipoComprobante->nombre }}</p></td>
                      <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p>{{ $pago->numero_operacion }}</p></td>
                      <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p>{{ $pago->tipoMoneda->nombre }} {{$pago->monto_equivalente }}</p></td>
                    </tr>
                     @endforeach
                    @endforeach
                @endforeach


                  </table>
                </div>

    <div class="footer">
      <p>Documento generado con Jarvis Frontdesk</p>
    </div>


  </body>
</html>