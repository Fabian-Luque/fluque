<?php

namespace App\Http\Controllers;

use App\Calendario;
use App\Cliente;
use App\Habitacion;
use App\TipoFuente;
use App\MetodoPago;
use App\EstadoReserva;
use App\Http\Controllers\Controller;
use App\Reserva;
use App\Huesped;
use App\Pago;
use App\TipoHabitacion;
use App\Propiedad;
use Illuminate\Http\Request;
use Response;
use \Carbon\Carbon;
use Validator;


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

    public function reserva(Request $request)
    {

/*        $reserva = Reserva::all()->last();

        return $reserva->monto_total + 1;*/

        $clientes = $request['cliente'];
      
        $habitaciones_info = $request['habitacion_info'];

        if (!is_array($habitaciones_info)) {
            $habitaciones_info = [];
            $habitaciones_info . push($request['habitacion_info']);
        }

        $fecha_inicio = $request->input('fecha_inicio');
        $fecha_fin    = $request->input('fecha_fin');

        if ($clientes['tipo_cliente_id'] == 1) {

                $cliente = Cliente::firstOrNew($request['cliente']);

                $cliente->rut                   = $clientes['rut'];
                $cliente->tipo_cliente_id       = $clientes['tipo_cliente_id'];
                $cliente->direccion             = $clientes['direccion'];
                $cliente->ciudad                = $clientes['ciudad'];
                $cliente->pais                  = $clientes['pais'];
                $cliente->telefono              = $clientes['telefono'];
                $cliente->giro                  = null;
                $cliente->save();

            } else {

            if ($clientes['tipo_cliente_id'] == 2) {

                $cliente = Cliente::firstOrNew($request['cliente']);

                $cliente->rut               = $clientes['rut'];
                $cliente->tipo_cliente_id   = $clientes['tipo_cliente_id'];
                $cliente->direccion         = $clientes['direccion'];
                $cliente->ciudad            = $clientes['ciudad'];
                $cliente->pais              = $clientes['pais'];
                $cliente->telefono          = $clientes['telefono'];
                $cliente->giro              = $clientes['giro'];
                $cliente->save();
            }

        }

            foreach ($habitaciones_info as $habitacion_info) {
                $huespedes = $habitacion_info['huespedes'];
                $propiedad_id = $habitacion_info['propiedad_id'];

                $reservas = Reserva::whereHas('habitacion', function($query) use($propiedad_id){

                    $query->where('propiedad_id', $propiedad_id);})->get();

                $reserva = $reservas->last();

                if(!empty($reserva)){

                $numero = $reserva->numero_reserva;

                }else{

                $numero = 0;    

                }


                $reserva                        = new Reserva();
                if(!empty($reserva)){
                $reserva->numero_reserva        = $numero + 1;
                }else{
                $reserva->numero_reserva        = 1;
                }
                $reserva->monto_alojamiento     = $habitacion_info['monto_alojamiento'];
                $reserva->monto_total           = $habitacion_info['monto_alojamiento'];
                $reserva->monto_por_pagar       = $habitacion_info['monto_alojamiento'];
                $reserva->ocupacion             = $habitacion_info['ocupacion'];
                $reserva->tipo_fuente_id        = $request['tipo_fuente_id'];
                $reserva->habitacion_id         = $habitacion_info['id'];
                $reserva->cliente_id            = $cliente->id;
                $reserva->checkin               = $fecha_inicio;
                $reserva->checkout              = $fecha_fin;
                $reserva->estado_reserva_id     = $request['estado_reserva_id'];
                $reserva->noches                = $request['noches'];
                $reserva->save();

            if(!empty($huespedes)){

           foreach ($huespedes as $huesped) {
                
                $huesped = Huesped::firstOrNew($huesped);
              
                $huesped->apellido       = $huesped['apellido'];
                $huesped->rut            = $huesped['rut'];
                $huesped->telefono       = $huesped['telefono'];
                $huesped->pais           = $huesped['pais'];
                $huesped->save();

                $reserva->huespedes()->attach($huesped->id);

           }



        }


            $fecha = $fecha_inicio;

            while (strtotime($fecha) < strtotime($fecha_fin)) {

                $calendario = Calendario::where('fecha', '=', $fecha)->where('habitacion_id', '=', $habitacion_info['id'])->first();

                $calendario->disponibilidad--;
                $calendario->reservas++;
                $calendario->save();

                $fecha = date("Y-m-d", strtotime("+1 day", strtotime($fecha)));
            }
        }

        return 'Habitacion reservada satisfactoriamente';
    }

    public function index(Request $request)
    {
        if ($request->has('fecha_inicio') && $request->has('fecha_fin') && $request->has('propiedad_id')) {
            try {
                $fechaInicio = new Carbon($request->fecha_inicio);
                $fechaFin    = new Carbon($request->fecha_fin);
            } catch (\Exception $e) {
                $return['status']  = 'error';
                $return['message'] = 'Las fechas no corresponden';
                return response()
                    ->json($return)
                    ->header('Access-Control-Allow-Origin', '*');
            }

            if ($fechaInicio < $fechaFin) {
                $fechas       = [$fechaInicio, $fechaFin];
                $habitaciones = Habitacion::where('propiedad_id', $request->propiedad_id)
                    ->where(function ($query) use ($fechas) {
                        $query->whereHas('reservas', function ($query) use ($fechas) {
                            $query->whereBetween('checkin', $fechas);
                        });
                        $query->orWhereHas('reservas', function ($query) use ($fechas) {
                            $query->whereNotBetween('checkin', $fechas);
                        });
                        $query->orHas('reservas', '=', 0);
                    })->with('tipoHabitacion')->with('reservas.habitacion.tipoHabitacion','reservas.cliente','reservas.huespedes' ,'reservas.tipoFuente', 'reservas.metodoPago', 'reservas.estadoReserva')->get();

                foreach ($habitaciones as $habitacion) {
                    $dias     = array();
                    $auxFecha = new Carbon($fechaInicio);
                    while ($auxFecha <= $fechaFin) {
                        $dia             = new \stdClass();
                        $dia->reserva_id = null;
                        foreach ($habitacion->reservas as $reserva) {
                            if (new Carbon($reserva->checkin) <= $auxFecha && $auxFecha < new Carbon($reserva->checkout)) {
                                $dia->reserva_id = $reserva->id;
                            }
                        }
                        $dia->fecha = $auxFecha->toDateString();
                        $dias[]     = $dia;
                        $auxFecha->addDay();
                    }
                    $habitacion->dias = $dias;
                }
                $return['habitaciones'] = $habitaciones;
                $return['status']       = 'ok';
            } else {
                $return['status']  = 'error';
                $return['message'] = 'Las fechas no corresponden';
            }
        } else {
            $return['status']  = 'error';
            $return['message'] = 'La solicitud está incompleta.';
        }

        return response()
            ->json($return)
            ->header('Access-Control-Allow-Origin', '*');
    }





    public function getReservas(Request $request){




              if($request->has('propiedad_id')){



                $id = $request->input('propiedad_id');


                $reservas = Reserva::whereHas('habitacion', function($query) use($id){

                    $query->where('propiedad_id', $id);



                })->with('habitacion.tipoHabitacion')->with('pagos')->with('cliente.tipoCliente')->with('huespedes.servicios')->with('tipoFuente', 'metodoPago', 'estadoReserva')->get();


                foreach ($reservas as $reserva){
                    
                    foreach ($reserva['huespedes'] as $huesped) {
                        $huesped->consumo_total = 0;
                        foreach ($huesped['servicios'] as $servicio) {
                            $huesped->consumo_total += $servicio->pivot->precio_total;

                        }
                    }

                }






                $data = ['reservas' => $reservas,];

                return $data;

                }else{

                    $retorno = array(

                    'msj'       => "No se envia id propiedad",
                    'errors'    => true


                    );

                     return Response::json($retorno, 400);

        }

        
    }

    public function pagoReserva(Request $request){

      $metodo_pago = $request->input('metodo_pago_id');
      $monto_pago =  $request->input('monto_pago'); 
      $reserva = Reserva::where('id', $request->input('reserva_id'))->first();

      if(is_null($metodo_pago)){

            if($reserva->estado_reserva_id == 1){

               $monto = $reserva->monto_por_pagar;
               $monto -= $monto_pago;

               $pago                        = new Pago();
               $pago->monto_pago            = $monto_pago;
               $pago->tipo                  = "Confirmacion de pago";
               $pago->reserva_id            = $reserva->id;
               $pago->save();

               $reserva->update(array('monto_por_pagar' => $monto , 'estado_reserva_id' => 2));

               $data = array(

                'msj' => "Deposito sugerido ingresado satisfactoriamente",
                'errors' =>false
               );

                return Response::json($data, 200);



            }else{

                $data = array(

                'msj' => "No permitido",
                'errors' =>true
                );

                return Response::json($data, 400);



            }


      }else{


               $monto = $reserva->monto_por_pagar;
               $monto -= $monto_pago;

               if($reserva->monto_por_pagar > 0){

                   if($monto_pago <= $reserva->monto_por_pagar){

                 

                   $pago                        = new Pago();
                   $pago->monto_pago            = $monto_pago;
                   $pago->tipo                  = "Pago parcial o total";
                   $pago->reserva_id            = $reserva->id;
                   $pago->save();

                   $reserva->update(array('monto_por_pagar' => $monto, 'metodo_pago_id' => $metodo_pago));

                   $data = array(

                    'msj' => "Pago ingresado satisfactoriamente",
                    'errors' =>false
                   );

                    return Response::json($data, 200);

                }elseif($monto_pago > $reserva->monto_por_pagar){

                    $data = array(

                    'msj' => "El monto ingresado es mayor al monto por pagar",
                    'errors' =>true
                    );

                    return Response::json($data, 400);


                }
            }else{


                $data = array(

                'msj' => "La reserva ya fue pagada",
                'errors' =>true
                );

                return Response::json($data, 400);



            }


      }


    }

    public function panel(Request $request){


        $id = $request->input('propiedad_id');
        $fecha = $request->input('fecha_actual');
        $fecha_inicio = $request->input('fecha_inicio');
        $fecha_fin = $request->input('fecha_fin');


        $entradas = Reserva::whereHas('habitacion', function($query) use($id){

                    $query->where('propiedad_id', $id);

        })->where('checkin', $fecha)->with('habitacion.tipoHabitacion')->with('huespedes')->with('cliente')->with('estadoReserva')->get();


        $salidas = Reserva::whereHas('habitacion', function($query) use($id){

                    $query->where('propiedad_id', $id);

        })->where('checkout', $fecha)->with('habitacion.tipoHabitacion')->with('huespedes')->with('cliente')->with('estadoReserva')->get();

        $habitaciones_occupadas = Reserva::whereHas('habitacion', function($query) use($id){

                    $query->where('propiedad_id', $id);

        })->where('estado_reserva_id', 3)->get();



        $cantidad_entradas = count($entradas);
        $cantidad_salidas  = count($salidas); 
        $cantidad_ocupadas = count($habitaciones_occupadas); 






        $fechaInicio=strtotime($fecha_inicio);
        $fechaFin=strtotime($fecha_fin);
        $ocupacion = [];

        $propiedad = Propiedad::where('id', $id)->first();
        $numero_habitaciones = $propiedad->numero_habitaciones;



        for($i=$fechaInicio; $i<=$fechaFin; $i+=86400){
            
            $fecha = date("Y-m-d", $i);

        $reservas = Reserva::whereHas('habitacion', function($query) use($id){

                    $query->where('propiedad_id', $id);

        })->where('checkin','<=' ,$fecha)->where('checkout', '>', $fecha)->get();


        $porcentaje = count($reservas)*100 / $numero_habitaciones;

        $ocupacion_fecha = [

            
                'date' =>$fecha,
                'value' =>round($porcentaje)

        ];


        array_push($ocupacion, $ocupacion_fecha);

        unset($ocupacion_fecha);

        }

        $data = array(

            'cantidad_entradas' => $cantidad_entradas,
            'cantidad_salidas'  => $cantidad_salidas,
            'habitaciones_ocupadas' => $cantidad_ocupadas,
            'entradas' => $entradas,
            'salidas'  => $salidas,
            'porcentaje_ocupacion' => $ocupacion


            );

        return $data;

    }


    public function calendario(Request $request){


        $id = $request->input('propiedad_id');
        $fecha_inicio = $request->input('fecha_inicio');
        $fecha_fin    = $request->input('fecha_fin');
        $ancho_calendario = $request->input('ancho_calendario');
        $ancho_celdas = $request->input('ancho_celdas');
        $calendario = [];


        $fechas = [$fecha_inicio, $fecha_fin];


       $tipos = TipoHabitacion::whereHas('habitaciones', function($query) use($id){

                    $query->where('propiedad_id', $id);

        })->with(['habitaciones' => function ($q) use($id) {

        $q->where('propiedad_id', $id);}])->get();


/*with('tipoHabitacion')->with('reservas.habitacion.tipoHabitacion','reservas.cliente','reservas.huespedes' ,'reservas.tipoFuente', 'reservas.metodoPago', 'reservas.estadoReserva')*/

        $reservas = Reserva::whereHas('habitacion', function($query) use($id){

                    $query->where('propiedad_id', $id);

        })->with('habitacion.tipoHabitacion')->with('cliente','huespedes','tipoFuente', 'metodoPago','estadoReserva')->whereBetween('checkin', $fechas)->get();




        $habitaciones_tipo = [];

        foreach ($tipos as $tipo) {
                
            $habitaciones = $tipo->habitaciones;
            $nombre_tipo = $tipo->nombre;

            $nombre = [ 'nombre' => $nombre_tipo, 'header' => 1];
            array_push($habitaciones_tipo, $nombre);

            foreach ($habitaciones as $habitacion) {
                
                $nombre_habitacion = $habitacion->nombre;
                $hab = [ 'nombre' => $nombre_habitacion];
                array_push($habitaciones_tipo, $hab);



            }


        }



        $reservas_calendario = [];
        foreach ($reservas as $reserva) {
       
            $noches = $reserva->noches;
            $dia = date ("j",strtotime($reserva->checkin));
            $reserva->left = $dia * $ancho_celdas;

            $reserva->right = $ancho_calendario - $reserva->left - ($noches * $ancho_celdas);

            array_push($reservas_calendario, $reserva);




        }

        array_push($calendario, $habitaciones_tipo);
        array_push($calendario, $reservas_calendario);

        return $calendario;




    }



    public function update(Request $request, $id){



        $rules = array(

            'monto_sugerido'    => 'numeric',
            'monto_por_pagar'   => 'numeric',
            'estado_reserva_id' => 'numeric',


            );


        $validator = Validator::make($request->all(), $rules);


         if ($validator->fails()) {

            $data = [

                'errors' => true,
                'msg' => $validator->messages(),

            ];

            return Response::json($data, 400);

        } else {

            $propiedad = Reserva::findOrFail($id);
            $propiedad->update($request->all());
            $propiedad->touch();

            $data = [

                'errors' => false,
                'msg' => 'Reserva actualizada satisfactoriamente',

            ];

            return Response::json($data, 201);

        }




    }



    public function getTipoFuente(){

        $TipoFuente = TipoFuente::all();
            return $TipoFuente;


    }

    public function getMetodoPago(){

      

        $MetodoPago = MetodoPago::all();



        $respuesta = [

        'Metodo_pago' => $MetodoPago,

        ];


        return $respuesta;


    }


    public function getEstadoReserva(){

      

        $EstadoReserva = EstadoReserva::all();



        $respuesta = [

        'Estado_reserva' => $EstadoReserva,

        ];


        return $respuesta;


    }



}


