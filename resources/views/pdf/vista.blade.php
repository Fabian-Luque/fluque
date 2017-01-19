<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>

	  <table>
		<tr>  
				<th>numero reserva</th>
				<th>monto alojamiento</th>
				<th>monto consumo</th>
				<th>monto total</th>
				<th>monto por pagar</th>
				<th>ocupacion</th>
				<th>checkin</th>
				<th>checkout</th>
				<th>noches</th>

		</tr>
 
            @foreach($reservas as $reserva)
            
                <tr>      
                    <td>{{ $reserva->numero_reserva }}</td>
                    <td>{{ $reserva->monto_alojamiento }}</td>
                    <td>{{ $reserva->monto_consumo }}</td>
                    <td>{{ $reserva->monto_total }}</td>
                    <td>{{ $reserva->monto_por_pagar }}</td>
                    <td>{{ $reserva->ocupacion }}</td>
                    <td>{{ $reserva->checkin }}</td>
                    <td>{{ $reserva->checkout }}</td>
                    <td>{{ $reserva->noches }}</td>
                </tr>
            @endforeach
 
    </table>


</body>
</html>