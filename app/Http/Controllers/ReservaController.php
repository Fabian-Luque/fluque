<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use Response;
use App\Cliente;
use App\Reserva;
use App\Calendario;
use App\DetalleNoche;

class ReservaController extends Controller
{
    

	 /**
     * realizar reservas para un cliente, de una o mas habitaciones
     *
     * @author ALLEN
     *
     * @param  Request          $request ()
     * @return Response::json
     */

	public function reserva(Request $request){

   	$habitaciones_info = $request['habitacion_info'];

    if (!is_array($habitaciones_info)){
        $habitaciones_info = [];
        $habitaciones_info.push($request['habitacion_info']);
    }


    $fecha_inicio = $request->input('fecha_inicio');
    $fecha_fin    = $request->input('fecha_fin');

    $cliente = Cliente::firstOrCreate($request['cliente']);

    foreach ($habitaciones_info as $habitacion_info) {

    	

        $reserva = new Reserva();
        $reserva->precio_total   = $habitacion_info['precio_base'];
        $reserva->ocupacion      = $request['ocupacion'];
        $reserva->cliente_id     = $cliente->id;
        $reserva->checkin        = $fecha_inicio;
        $reserva->checkout       = $fecha_fin;
        $reserva->save();

        $fecha = $fecha_inicio;

        while (strtotime($fecha) < strtotime($fecha_fin)){

            $calendario = Calendario::where('fecha','=', $fecha)->where('habitacion_id', '=', $habitacion_info['id'])->first();

            $noche = new DetalleNoche();
            $noche->precio            = $calendario->precio;
            $noche->fecha             = $fecha;
            $noche->habitacion_id     = $habitacion_info['id'];
            $noche->reserva_id        = $reserva->id;
            $noche->save();

            $calendario->disponibilidad--;
            $calendario->reservas++;
            $calendario->save();
      
            $fecha = date ("Y-m-d", strtotime("+1 day", strtotime($fecha)));
        } 
    }

    return 'Habitacion reservada satisfactoriamente';
}
	
    
    
}   





