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
        <h2 class="titulo">Detalle de pagos</h2>



        <div class="contenedor">
          <div class="estado-cuenta">




                    <!-- <div class="titulo" style="text-align:left;">
                      <h3 class="">{{ $fecha_inicio->format('d/m/Y') }} - {{ $fecha_fin->format('d/m/Y') }}</h3>
                    </div>

                    <div class="detalles" style="margin:20px 0px;">
                      <table class="tabla-detalles">
                      	
                        <tr>
                          <th class="data-tabla-detalles borde-derecha"><p>Fecha</p></th>
                @foreach($propiedad as $prop)
                	@foreach($prop['tipoMonedas'] as $moneda)
                          <th class="data-tabla-detalles borde-derecha"><p>{{ $moneda->nombre }}</p></th>
                    @endforeach
                @endforeach
                        </tr>
        		    @foreach($fechas as $fecha)
                        <tr style="text-align:center;">
                          <td class="data-tabla-detalles borde-derecha"><p>{{ $fecha['fecha'] }}</p></td>
        			  @foreach($fecha['moneda'] as $moneda)
                          <td class="data-tabla-detalles borde-derecha"><p>{{ $moneda['nombre'] }} ${{ $moneda['suma'] }}</p></td>
                    @endforeach
                        </tr>
        		    @endforeach
                      
                        <tr style="text-align:center;">
                          <td class="data-tabla-detalles borde-derecha"><p>Total</p></td>
                    @foreach($ingresos_totales as $total)
                          <td class="data-tabla-detalles borde-derecha"><p>{{ $total['nombre_moneda'] }} ${{ $total['monto'] }}</p></td>
                    @endforeach
                        </tr>

                      </table>
                    </div> -->


                    <div class="detalles" style="margin:20px 0px;">
                        

                      @foreach($fechas as $fecha)
                        <div class="listado-fechas">

                          <div class="data-tabla-detalles"><p style="font-size:16px;">{{ $fecha['fecha'] }}</p></div>

                          
                          <div class="data-tabla-detalles" style="height:40px;">
                            <div class="" style="display: inline-block;width:120px;"><p style="font-size: 13px; font-weight: bold;">Pago total</p></div>
                            @foreach($fecha['moneda'] as $moneda)
                            <div style="display: inline-block;width:50%;"><p>{{ $moneda['nombre'] }} ${{ $moneda['suma'] }}</p></div>
                            @endforeach
                          </div>



                     <div class="detalles" style="margin:0px 0px 20px 0px;">
                      <table class="tabla-detalles">
                        <tr>
                         <th class="data-tabla-detalles borde-derecha"><p>Nº Reserva</p></th>
                          <th class="data-tabla-detalles borde-derecha"><p>Tipo de pago</p></th>
                          <th class="data-tabla-detalles borde-derecha"><p>Método de pago</p></th>
                          <th class="data-tabla-detalles borde-derecha"><p>Tipo de comprobante</p></th>
                          <th class="data-tabla-detalles borde-derecha"><p>Nº de operación</p></th>
                          <th class="data-tabla-detalles borde-derecha"><p>Monto</p></th>
                        </tr>




                        @foreach($fecha['ps'] as $pago)
                        <tr>
                          <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p> {{ $pago->numero_reserva }} </p></td>
                          <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p> {{ $pago->tipo }} </p></td>
                          <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p> {{ $pago->nombre_metodo_pago }},  {{ $pago->numero_cheque }} </p></td>
                          <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p> {{ $pago->tipoComprobante->nombre }} </p></td>
                          <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p> {{ $pago->numero_operacion }} </p></td>
                          <td class="data-tabla-detalles borde-derecha" style="text-align:center;"><p> {{ $pago->nombre_tipo_moneda }} ${{ $pago->monto_equivalente }} </p></td>
                        </tr>
                        @endforeach


                      </table>
                    </div>


                        </div>
                @endforeach


                    </div>




                   
                    


                    






          </div>
          <!--  Fin estado-cuenta  -->
        </div>
        <!-- Fin contenedor -->
      </div>
    </div>
      


    <div class="footer">
      <p>Documento generado con Jarvis Frontdesk</p>
    </div>


  </body>
</html>
