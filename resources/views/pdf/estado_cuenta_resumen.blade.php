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

      @if(!empty($propiedad))   
        @foreach($propiedad as $prop)
          <h2>{{$prop->nombre}}</h2>
          <h3>{{$prop->email}}</h3>
          <h3>{{$prop->direccion}}</h3>
          <h3>{{$prop->ciudad}}</h3>
          <h3>{{$prop->pais->nombre}}</h3>
        @endforeach
      @endif
        </div>
      </div>

      <div class="estado-cuenta">
        <h2 class="titulo">Estado de cuenta</h2>
        <div class="cliente">
                <table class="tabla-cliente">
                  @if(!empty($cliente))
                    @if($cliente->tipo_cliente_id == 1)
                      @if($cliente->pais_id == null)
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Nombre</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cliente->nombre }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Apellido</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cliente->apellido }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Rut</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cliente->rut }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Dirección</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cliente->direccion }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Ciudad</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cliente->ciudad }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">País</p></th>
                        <td class="data-tabla-cliente"><p class="nombre"></p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Teléfono</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cliente->telefono }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Email</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cliente->email }}</p></td>
                    </tr>
                      @else
                      <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Nombre</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cliente->nombre }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Apellido</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cliente->apellido }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Rut</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cliente->rut }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Dirección</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cliente->direccion }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Ciudad</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cliente->ciudad }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">País</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cliente->pais->nombre }}</p><</td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Teléfono</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cliente->telefono }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Email</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cliente->email }}</p></td>
                    </tr>
                     @endif

                    @else
                      @if($cliente->pais_id == null)

                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Razon social</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cliente->nombre }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Rut</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cliente->rut }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Giro</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cliente->giro }}</p></td>
                    </tr>

                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Dirección</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cliente->direccion }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Ciudad</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cliente->ciudad }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">País</p></th>
                        <td class="data-tabla-cliente"><p class="nombre"></p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Teléfono</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cliente->telefono }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Email</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cliente->email }}</p></td>
                    </tr>
                      @else
                      <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Razon social</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cliente->nombre }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Rut</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cliente->rut }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Giro</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cliente->giro }}</p></td>
                    </tr>

                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Dirección</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cliente->direccion }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Ciudad</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cliente->ciudad }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">País</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cliente->pais->nombre }}</p><</td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Teléfono</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cliente->telefono }}</p></td>
                    </tr>
                    <tr>
                        <th class="head-tabla-cliente"><p class="align-left">Email</p></th>
                        <td class="data-tabla-cliente"><p class="nombre">{{ $cliente->email }}</p></td>
                    </tr>
                      @endif
                    @endif
                @endif
                </table>
        </div>
        <!--  Fin cliente  -->
        <div class="detalles">
          <div class="titulo">
            <h3 class="">Detalles de las reservas/estadías</h3>
          </div>

          <table class="tabla-detalles">
          @foreach($reservas_pdf as $reserva)

            <tr>
              <td class="data-tabla-detalles borde-derecha"><p class="titulo">Reserva Nº @if(!empty($reserva->numero_reserva)) {{ $reserva->numero_reserva }} @endif - Habitacion  @if(!empty($reserva->habitacion->nombre)) {{ $reserva->habitacion->nombre }} @endif - @if(!empty($reserva->habitacion->tipoHabitacion->nombre)) {{ $reserva->habitacion->tipoHabitacion->nombre }} @endif - @if(!empty($reserva->ocupacion)) {{ $reserva->ocupacion }} @endif  Huéspedes - @if(!empty($reserva->noches)) {{ $reserva->noches }} @endif Noches - Checkin @if(!empty($reserva->checkin)) {{ $reserva->checkin->format('d-m-Y') }} @endif - Checkout @if(!empty($reserva->checkout)) {{ $reserva->checkout->format('d-m-Y') }} @endif </p></td>
              <td class="data-tabla-detalles-right align-right"><p class="nombre">@if(!empty($reserva->tipoMoneda->nombre)) {{ $reserva->tipoMoneda->nombre }} @endif @if(!empty($reserva->monto_alojamiento)) ${{  number_format($reserva->monto_alojamiento)  }} @endif </p></td>
            </tr>
            @endforeach
         


              @if($nombre_moneda == "CLP")
                @if($iva_reservas == 1)


                <tr>
                  <th class="data-tabla-detalles borde-derecha"><p class="titulo align-right">Subtotal</p></th>
                  <td class="data-tabla-detalles-right align-right"><p class="nombre">{{ $nombre_moneda }} ${{ number_format($neto) }}</p></td>
                </tr>
                <tr>
                  <th class="data-tabla-detalles borde-derecha"><p class="titulo align-right">IVA</p></th>
                  <td class="data-tabla-detalles-right align-right"><p class="nombre">{{ $nombre_moneda }} ${{ number_format($iva)}}</p></td>
                </tr>
                <tr>
                  <th class="data-tabla-detalles borde-derecha"><p class="titulo align-right">Total</p></th>
                  <td class="data-tabla-detalles-right align-right"><p class="nombre">{{ $nombre_moneda }} ${{ number_format($total) }}</p></td>
                </tr>

                @else
                <tr>
                  <th class="data-tabla-detalles borde-derecha"><p class="titulo align-right">Total</p></th>
                  <td class="data-tabla-detalles-right align-right"><p class="nombre">{{ $nombre_moneda }} ${{ number_format($total) }}</p></td>
                </tr>

                @endif

              @else

            <tr>
              <th class="data-tabla-detalles borde-derecha"><p class="titulo align-right">Total</p></th>
              <td class="data-tabla-detalles-right align-right"><p class="nombre">{{ $nombre_moneda }} ${{ $total }}</p></td>
            </tr>


              @endif

          </table>


          <div class="titulo" style="text-align:left;">
            <h3 class="">Detalle de pagos</h3>
          </div>

          <div class="detalles" style="margin:20px 0px;">
            <table class="tabla-detalles">
              <tr>
               <th class="data-tabla-detalles borde-derecha"><p>Nº Reserva</p></th>
                <th class="data-tabla-detalles borde-derecha"><p>Tipo de pago</p></th>
                <th class="data-tabla-detalles borde-derecha"><p>Fecha</p></th>
                <th class="data-tabla-detalles borde-derecha"><p>Método de pago</p></th>
                <th class="data-tabla-detalles borde-derecha"><p>Tipo de comprobante</p></th>
                <th class="data-tabla-detalles borde-derecha"><p>Nº de operación</p></th>
                <th class="data-tabla-detalles borde-derecha"><p>Monto</p></th>
              </tr>
              @if(!empty($reservas_pdf))
                @foreach($reservas_pdf as $reserva)
                  @if(!empty($reserva->pagos))
                     @foreach($reserva->pagos as $pago)
                        <tr>
                          <td class="data-tabla-detalles borde-derecha" style="text-align:center;">
                            <p>
                              {{ $reserva->numero_reserva }}
                            </p>
                          </td>
                          <td class="data-tabla-detalles borde-derecha" style="text-align:center;">
                            <p>
                              {{ $pago->tipo }}
                            </p>
                          </td>
                          <td class="data-tabla-detalles borde-derecha" style="text-align:center;">
                            <p>
                              {{ $pago->created_at }}
                            </p>
                          </td>
                          
                          @if($pago->numero_cheque == null)
                          <td class="data-tabla-detalles borde-derecha" style="text-align:center;">
                            <p>
                              {{ $pago->metodoPago->nombre }}
                            </p>
                          </td>
                          @else
                          <td class="data-tabla-detalles borde-derecha" style="text-align:center;">
                            <p>
                              {{ $pago->metodoPago->nombre }} {{$pago->numero_cheque }}
                            </p>
                          </td>
                          @endif

                          @if($pago->tipoComprobante == null)
                          <td class="data-tabla-detalles borde-derecha" style="text-align:center;">
                            <p>
                              
                            </p>
                          </td>
                          @else
                          <td class="data-tabla-detalles borde-derecha" style="text-align:center;">
                            <p>
                              {{ $pago->tipoComprobante->nombre }}
                            </p>
                          </td>
                          @endif

                          @if($pago->numero_operacion == null)
                          <td class="data-tabla-detalles borde-derecha" style="text-align:center;">
                            <p>
                              
                            </p>
                          </td>
                          @else
                          <td class="data-tabla-detalles borde-derecha" style="text-align:center;">
                            <p>
                              {{ $pago->numero_operacion }}
                            </p>
                          </td>
                          @endif
                          
                          @if($pago->tipo_moneda_id == 1)
                            <td class="data-tabla-detalles borde-derecha" style="text-align:center;">
                              <p>
                                {{ $pago->tipoMoneda->nombre }} ${{ number_format($pago->monto_equivalente) }}
                              </p>
                            </td>
                          @else 
                            <td class="data-tabla-detalles borde-derecha" style="text-align:center;">
                              <p>
                                {{ $pago->tipoMoneda->nombre }} ${{ $pago->monto_equivalente }}
                              </p>
                            </td>
                          @endif
                        </tr>
                     @endforeach
                  @endif
                @endforeach
              @endif
            </table>
          </div>
        </div>
        <!--  Fin detalles  -->
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
  </body>
</html>