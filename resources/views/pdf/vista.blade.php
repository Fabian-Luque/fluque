<!DOCTYPE html>
<html>
    <head>
        <title></title>
      <link rel="stylesheet" type="text/css" href="css/app.css">
    </head>
    <body>
        <div class="encabezado">
      <div class="propiedad-info">

      @foreach($propiedad as $prop)
        <p>{{  $prop->nombre  }}</p>
        <p>{{  $prop->email  }}</p>
        <p>{{  $prop->direccion  }}</p>
        <p>{{  $prop->ciudad  }}</p>
        <p>{{  $prop->pais  }}</p>
      @endforeach
            </div>

      </div>

        <div class="cliente">
            <table class="tabla-cliente">

            @foreach($cliente as $cte)
                <tr>
                    <th><p class="titulo align-left">razon social</p></th>
                    <td><p class="nombre">{{ $cte->nombre }}</p></td>
                </tr>
                <tr>
                    <th><p class="titulo align-left">giro</p></th>
                    <td><p class="nombre">{{ $cte->giro }}</p></td>
                </tr>
                <tr>
                    <th><p class="titulo align-left">rut</p></th>
                    <td><p class="nombre">{{ $cte->rut }}</p></td>
                </tr>
                <tr>
                    <th><p class="titulo align-left">Dirección</p></th>
                    <td><p class="nombre">{{ $cte->direccion }}</p></td>
                </tr>
                <tr>
                    <th><p class="titulo align-left">Ciudad</p></th>
                    <td><p class="nombre">{{ $cte->ciudad }}</p></td>
                </tr>
                <tr>
                    <th><p class="titulo align-left">País</p></th>
                    <td><p class="nombre">{{ $cte->pais }}</p></td>
                </tr>
                <tr>
                    <th><p class="titulo align-left">Teléfono</p></th>
                    <td><p class="nombre">{{ $cte->telefono }}</p></td>
                </tr>
                <tr>
                    <th><p class="titulo align-left">Email</p></th>
                    <td><p class="nombre">{{ $cte->email }}</p></td>
                </tr>

            @endforeach
            </table>
        </div>

        <div class="detalles">
            <h3 style="margin-bottom:10px;">Detalles</h3>
            <table class="tabla-detalles">

            @foreach($reservas_pdf as $reservas)
                @foreach($reservas as $reserva)
                <tr>
                    <td><p class="titulo">Reserva {{ $reserva->numero_reserva }}, habitacion: {{ $reserva->habitacion->nombre }}, checkin: {{ $reserva->checkin }}, checkout: {{ $reserva->checkout }}, noches: {{ $reserva->noches }}</p></td>
                    <td><p class="nombre">${{ $reserva->monto_total }}</p></td>
                </tr>

                @endforeach
            @endforeach
                <tr>
                    <th><p class="titulo align-right">Neto</p></th>
                    <td><p class="nombre">${{ $neto }}</p></td>
                </tr>
                <tr>
                    <th><p class="titulo align-right">iva</p></th>
                    <td><p class="nombre">${{ $iva }}</p></td>
                </tr>
                <tr >
                    <th><p class="titulo align-right">Total</p></th>
                    <td><p class="nombre">${{ $total }}</p></td>
                </tr>

            </table>
        </div>





    </body>
</html>