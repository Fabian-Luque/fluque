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

                </table>
        </div>
        <!--  Fin cliente  -->
        <div class="detalles">
          <div class="titulo">
            <h3 class="">Detalles de las reservas/estadías</h3>
          </div>

          <table class="tabla-detalles">
          
            @if(!empty($reservas_pdf))
            @foreach($reservas_pdf as $reserva)
            <tr>
              <td class="data-tabla-detalles borde-derecha"><p class="titulo">Reserva Nº {{ $reserva->numero_reserva }} - Habitacion {{ $reserva->habitacion->nombre }} - {{ $reserva->habitacion->tipoHabitacion->nombre }} - {{ $reserva->ocupacion }} Huéspedes - {{ $reserva->noches }} Noches - Checkin {{ $reserva->checkin->format('d-m-Y') }} - Checkout {{ $reserva->checkout->format('d-m-Y') }}</p></td>
              <td class="data-tabla-detalles-right align-right"><p class="nombre">{{ $reserva->tipoMoneda->nombre }} ${{  number_format($reserva->monto_alojamiento)  }}</p></td>
            </tr>
            @endforeach
            @endif
       
              @if($nombre_moneda == "CLP")
                  @if($iva_reservas == 1)
                    <tr>
                      <th class="data-tabla-detalles borde-derecha"><p class="titulo align-right">Subtotal</p></th>
                      <td class="data-tabla-detalles-right align-right"><p class="nombre">{{ $nombre_moneda }} ${{ number_format($neto) }}</p></td>
                    </tr>
                    <tr>
                      <th class="data-tabla-detalles borde-derecha"><p class="titulo align-right">IVA</p></th>
                      <td class="data-tabla-detalles-right align-right"><p class="nombre">{{ $nombre_moneda }} ${{  number_format($iva) }}</p></td>
                    </tr>
                    <tr>
                      <th class="data-tabla-detalles borde-derecha"><p class="titulo align-right">Total</p></th>
                      <td class="data-tabla-detalles-right align-right"><p class="nombre">{{ $nombre_moneda }} ${{   number_format($total)}}</p></td>
                    </tr>
                  @else
                    <tr>
                      <th class="data-tabla-detalles borde-derecha"><p class="titulo align-right">Total</p></th>
                      <td class="data-tabla-detalles-right align-right"><p class="nombre">{{ $nombre_moneda }} ${{ number_format($total)}}</p></td>
                    </tr>
                @endif

              @else
                <tr>
                  <th class="data-tabla-detalles borde-derecha"><p class="titulo align-right">Total</p></th>
                  <td class="data-tabla-detalles-right align-right"><p class="nombre">{{ $nombre_moneda }} ${{ $total  }}</p></td>
                </tr>
              @endif
          </table>
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
    <div class="page-break"></div>

    <div class="contenedor">

      <div class="detalle-consumo">
        <h2 class="titulo">Detalle de consumos</h2>
     
        <h3 class="margen">Cliente principal de la reserva: 
          <span>{{ $cliente->nombre }} {{ $cliente->apellido }}</span>
        </h3>
        
        <div class="">
        @if(!empty($reservas_pdf))
          @foreach($reservas_pdf as $reserva)
            @if(!empty($reserva->huespedes))
            
              @foreach($reserva->huespedes as $huesped)
              <?php 
     
                        $total =  $huesped->monto_consumo;
                        
                    
                   ?>
                <p class="negrita">{{ $huesped->nombre }} {{ $huesped->apellido }}</p>
                <p class="negrita">
                  Reserva Nº {{ $reserva->numero_reserva }} - Habitacion {{ $reserva->habitacion->nombre }} - {{ $reserva->habitacion->tipoHabitacion->nombre }}
                </p>

                  <table class="tabla-comsumos margen">
                  @foreach($huesped->servicios as $servicio)
                  
                    <tr>
                      <td class="data-tabla-detalles borde-derecha">
                        <p class="">{{ $servicio->pivot->created_at->format('d-m-Y') }}   -  {{ $servicio->pivot->cantidad }} {{ $servicio->nombre }}
                        </p>
                      </td>
                      @if($reserva->tipo_moneda_id == 1)
                        <td class="data-tabla-detalles-right align-right"><p class="nombre">{{ $reserva->tipoMoneda->nombre }} ${{  number_format($servicio->pivot->precio_total) }}</p></td>
                      @else 
                        <td class="data-tabla-detalles-right align-right"><p class="nombre">{{ $reserva->tipoMoneda->nombre }} ${{  $servicio->pivot->precio_total }}</p></td>
                      @endif
                    </tr>
                  @endforeach
                    <tr>
                    <th class="data-tabla-detalles borde-derecha">
                      <p class="titulo align-right">Total</p>
                    </th>
                    @if($reserva->tipo_moneda_id == 1)
                      <td class="data-tabla-detalles-right align-right"><p class="nombre">{{ $reserva->tipoMoneda->nombre }} ${{  number_format($total)}}</p></td>
                    @else 
                        <td class="data-tabla-detalles-right align-right">
                          <p class="nombre"> 
                            {{ $reserva->tipoMoneda->nombre }} ${{ $total }}
                          </p>
                        </td>
                    @endif
                  </tr>
                </table>
              @endforeach
            @endif
          @endforeach
        @endif
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
                        <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p>{{ $reserva->numero_reserva }}</p></td>
                        <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p>{{ $pago->tipo }}</p></td>
                        <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p>{{ $pago->created_at }}</p></td>
                        @if($pago->numero_cheque == null)
                        <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p>{{ $pago->metodoPago->nombre }}</p></td>
                        @else
                        <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p>{{ $pago->metodoPago->nombre }} {{$pago->numero_cheque }}</p></td>
                        @endif

                        @if($pago->tipoComprobante == null)
                        <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p></p></td>
                        @else
                        <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p>{{ $pago->tipoComprobante->nombre }}</p></td>
                        @endif

                        @if($pago->numero_operacion == null)
                        <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p></p></td>
                        @else
                        <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p>{{ $pago->numero_operacion }}</p></td>
                        @endif

                        @if($pago->tipo_moneda_id == 1)
                          <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p>{{ $pago->tipoMoneda->nombre }} ${{ number_format($pago->monto_equivalente) }}</p></td>

                          @else 
                          <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p>{{ $pago->tipoMoneda->nombre }} ${{ $pago->monto_equivalente }}</p></td>
                        @endif
                      </tr>
                      @endforeach
                     @endif
                    @endforeach
                  @endif
                </table>
              </div>
  </body>
</html>