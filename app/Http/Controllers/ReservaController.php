<?php

namespace App\Http\Controllers;


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
use App\Servicio;
use App\TipoComprobante;
use App\HuespedReservaServicio;
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
                $cliente->ciudad                = $clientes['ciudad'];
                $cliente->pais                  = $clientes['pais'];
                $cliente->giro                  = null;
                $cliente->save();

            } else {

            if ($clientes['tipo_cliente_id'] == 2) {

                $cliente = Cliente::firstOrNew($request['cliente']);

                $cliente->rut               = $clientes['rut'];
                $cliente->tipo_cliente_id   = $clientes['tipo_cliente_id'];
                $cliente->pais              = $clientes['pais'];
                $cliente->giro              = $clientes['giro'];
                $cliente->save();
            }

        }

            foreach ($habitaciones_info as $habitacion_info) {
                $habitacion_id = $habitacion_info['id'];
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

                $reserv =  Reserva::where('habitacion_id', $habitacion_info['id'])->where('checkin', $fecha_inicio)->where('checkout', $fecha_fin)->first();



                if(is_null($reserv)){

                   
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

            }else{

                    $retorno = array(

                    'msj'       => "La reserva ya fue creada",
                    'errors'    => true


                    );

                     return Response::json($retorno, 406);







           }


////////////////////////////////////////////////////////////////////////////////////////////////////

/*
            $fecha = $fecha_inicio;

            while (strtotime($fecha) < strtotime($fecha_fin)) {

                $calendario = Calendario::where('fecha', '=', $fecha)->where('habitacion_id', '=', $habitacion_info['id'])->first();

                $calendario->disponibilidad--;
                $calendario->reservas++;
                $calendario->save();

                $fecha = date("Y-m-d", strtotime("+1 day", strtotime($fecha)));
            }*/

/////////////////////////////////////////////////////////////////////////////////////////////////////
        }


                    $retorno = array(

                    'msj'       => "Reserva creada satisfactoriamente",
                    'errors'    => false


                    );

                     return Response::json($retorno, 201);

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

                $propiedad = Propiedad::where('id', $id)->first();

                if(!is_null($propiedad)){

                    
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


                $data = array(

                    'msj' => " No se encuentra propiedad",
                    'errors' => true


                );

                return Response::json($data, 404);




                }



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
      $numero_operacion = $request->input('numero_operacion');
      $tipo_comprobante_id = $request->input('tipo_comprobante_id');
      $tipo_pago = $request->input('tipo_pago');
      $reserva_id = $request->input('reserva_id');


      $reserva = Reserva::where('id', $request->input('reserva_id'))->first();

      if(is_null($metodo_pago)){


            if($reserva->estado_reserva_id == 1){

               $monto = $reserva->monto_por_pagar;
               $monto -= $monto_pago;

               $pago                        = new Pago();
               $pago->monto_pago            = $monto_pago;
               $pago->tipo                  = "Confirmacion de reserva";
               $pago->numero_operacion      = null;
               $pago->tipo_comprobante_id   = null;
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

            if($tipo_pago == "Pago reserva"){


               if($reserva->monto_por_pagar > 0){

                   if($monto_pago <= $reserva->monto_por_pagar){

                 

                   $pago                        = new Pago();
                   $pago->monto_pago            = $monto_pago;
                   $pago->tipo                  = "Pago reserva";
                   $pago->numero_operacion      = $numero_operacion;
                   $pago->tipo_comprobante_id  =  $tipo_comprobante_id;
                   $pago->reserva_id            = $reserva->id;
                   $pago->save();

                   $reserva->update(array('monto_por_pagar' => $monto, 'metodo_pago_id' => $metodo_pago));

                   $consumos = HuespedReservaServicio::where('reserva_id', $reserva_id)->get();

                    if(!is_null($consumos)){

                        foreach ($consumos as $consumo){
                            

                            $consumo->update(array('estado' => 'Pagado'));

                        }



                    }

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

            }elseif($tipo_pago == "Pago habitacion"){


              $pago_habitacion =  Pago::where('reserva_id', $reserva_id)->where('tipo', 'Pago habitacion')->first();

              $pago_total = Pago::where('reserva_id', $reserva_id)->where('tipo', 'Pago reserva')->first();

              if(is_null($pago_habitacion) && is_null($pago_total)){


                if($monto_pago <= $reserva->monto_por_pagar){


                   $pago                        = new Pago();
                   $pago->monto_pago            = $monto_pago;
                   $pago->tipo                  = "Pago habitacion";
                   $pago->numero_operacion      = $numero_operacion;
                   $pago->tipo_comprobante_id  =  $tipo_comprobante_id;
                   $pago->reserva_id            = $reserva->id;
                   $pago->save();

                   $reserva->update(array('monto_por_pagar' => $monto, 'metodo_pago_id' => $metodo_pago));

                   $data = array(

                    'msj' => "Pago ingresado satisfactoriamente",
                    'errors' =>false
                   );

                    return Response::json($data, 201);



                }else{


                    $data = array(

                    'msj' => "El monto a pagar no corresponde",
                    'errors' =>true
                    );

                    return Response::json($data, 400);


                }


              }else{


                    $data = array(

                    'msj' => "La habitacion ya fue pagada",
                    'errors' =>true
                    );

                    return Response::json($data, 400);



              }


            }


      }


    }


    public function pagoConsumo(Request $request){



        if($request->has('servicio_id') && $request->has('reserva_id')){

        $servicios = $request->input('servicio_id');
        $tipo_comprobante_id = $request->input('tipo_comprobante_id');
        $numero_operacion = $request->input('numero_operacion');


        $reserva_id = $request->input('reserva_id');

        $reserva = Reserva::where('id', $reserva_id)->first();

        if(!is_null($reserva)){

        $total_consumos = 0;
        $monto_por_pagar = $reserva->monto_por_pagar;

        $consumos_por_pagar = [];
        foreach($servicios as $servicio){
            $consumo =  HuespedReservaServicio::where('id', $servicio)->first();

            if($consumo->estado == 'Por pagar'){

                array_push($consumos_por_pagar, $consumo);



            }

        }

        if(count($consumos_por_pagar) > 0){

          foreach ($consumos_por_pagar as  $consumo) {
              
                $total_consumos += $consumo->precio_total;


          }

        }else{


                $retorno = array(

                'msj'    => "Los consumos ya fueron pagados",
                'errors' => true
                );

                return Response::json($retorno, 400);


        }


        $total = $monto_por_pagar - $total_consumos;

        if($total_consumos <= $reserva->monto_por_pagar){

            foreach ($servicios as $servicio) {
            
               $consumo =  HuespedReservaServicio::where('id', $servicio)->first();
               $consumo->update(array('estado' => 'Pagado'));


            }



                $reserva->update(array('monto_por_pagar' => $total));

                if($request->has('numero_operacion') && $request->has('tipo_comprobante_id')){

                $pago                        = new Pago();
                $pago->monto_pago            = $total_consumos;
                $pago->tipo                  = "Pago consumos";
                $pago->numero_operacion      = $numero_operacion;
                $pago->tipo_comprobante_id  =  $tipo_comprobante_id;
                $pago->reserva_id            = $reserva->id;
                $pago->save();


                }else{


                $pago                        = new Pago();
                $pago->monto_pago            = $total_consumos;
                $pago->tipo                  = "Pago consumos";
                $pago->numero_operacion      = null;
                $pago->tipo_comprobante_id  =  null;
                $pago->reserva_id            = $reserva->id;
                $pago->save();




                }

                $retorno = array(

                    'msj' => "Pago realizado correctamente",
                    'errors' =>false
                );

                return Response::json($retorno, 201);


        }else{


                $retorno = array(

                'msj'    => "No se puede realizar el pago",
                'errors' => true
                );

                return Response::json($retorno, 400);


        }

       

         }else{


                $data = array(

                    'msj' => "Reserva no encontrada",
                    'errors' => true


                );

            return Response::json($data, 404);


        }

            
        }else{


            $retorno = array(

                'msj'    => "La solicitud esta incompleta",
                'errors' => true
            );

            return Response::json($retorno, 400);

        }

    }




    public function panel(Request $request){


        $startDate = Carbon::today()->startOfDay();
        $endDate = Carbon::today()->endOfDay();


        $id = $request->input('propiedad_id');
        $fecha = $request->input('fecha_actual');
        $fecha_inicio = $request->input('fecha_inicio');
        $fecha_fin = $request->input('fecha_fin');

        $fecha_hoy = Carbon::today();


        $entradas = Reserva::whereHas('habitacion', function($query) use($id){

                    $query->where('propiedad_id', $id);

        })->where('checkin', $fecha)->where('estado_reserva_id', '!=', 3 )->with('habitacion.tipoHabitacion')->with('huespedes')->with('cliente')->with('estadoReserva')->get();

        $entradas_hoy = Reserva::whereHas('habitacion', function($query) use($id){

                    $query->where('propiedad_id', $id);})->where('checkin', $fecha)->get();


        $salidas = Reserva::whereHas('habitacion', function($query) use($id){

                    $query->where('propiedad_id', $id);

        })->where('checkout', $fecha)->where('estado_reserva_id', '!=' , 4)->with('habitacion.tipoHabitacion')->with('huespedes')->with('cliente')->with('estadoReserva')->get();

        $salidas_hoy = Reserva::whereHas('habitacion', function($query) use($id){

                    $query->where('propiedad_id', $id);})->where('checkout', $fecha)->get();

        $habitaciones_occupadas = Reserva::whereHas('habitacion', function($query) use($id){

                    $query->where('propiedad_id', $id);

        })->where('estado_reserva_id', 3)->get();



        $reservas_dia = Reserva::whereHas('habitacion', function($query) use($id){

                    $query->where('propiedad_id', $id);

        })->whereBetween('created_at', [$startDate, $endDate])->with('habitacion.tipoHabitacion')->with('huespedes')->with('cliente')->with('estadoReserva')->with('metodoPago')->with('tipoFuente')->get();



        $cantidad_entradas     = count($entradas_hoy);
        $cantidad_salidas      = count($salidas_hoy); 
        $cantidad_ocupadas     = count($habitaciones_occupadas); 
        $cantidad_reservas_dia = count($reservas_dia);




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

        $suma_monto_total = 0;
        $suma_noches = 0;
        foreach ($reservas_dia as $reserva_dia) {
            
            $monto_total = $reserva_dia->monto_total;
            $suma_monto_total += $monto_total;

            $noches = $reserva_dia->noches;
            $suma_noches += $noches;


        }

        $data = array(

            'cantidad_entradas' => $cantidad_entradas,
            'cantidad_salidas'  => $cantidad_salidas,
            'habitaciones_ocupadas' => $cantidad_ocupadas,
            'entradas' => $entradas,
            'salidas'  => $salidas,
            'cantidad_reservas_dia' => $cantidad_reservas_dia,
            'suma_monto_total' => $suma_monto_total,
            'suma_noches' => $suma_noches,
            'porcentaje_ocupacion' => $ocupacion,
            'reservas_dia' => $reservas_dia,


            );

        return $data;

    }



    public function ventas(Request $request){


      if($request->has('propiedad_id') && $request->has('fecha_inicio') && $request->has('fecha_fin') && $request->has('dias')){

        $propiedad_id = $request->input('propiedad_id');
        $fecha_inicio = $request->input('fecha_inicio');
        $fecha_fin = $request->input('fecha_fin');
        $dias = $request->input('dias');
        $rango = [$fecha_inicio, $fecha_fin];

        $propiedad = Propiedad::where('id', $propiedad_id)->first();

        $total_reservas = Reserva::whereHas('habitacion', function($query) use($propiedad_id){

            $query->where('propiedad_id', $propiedad_id);

        })->whereBetween('checkin' , $rango)->where('estado_reserva_id' , 4)->get();

        $fechaInicio=strtotime($fecha_inicio);
        $fechaFin=strtotime($fecha_fin);
        $numero_habitaciones = $propiedad->numero_habitaciones;
        
        $mes = $dias * $numero_habitaciones;
        $suma_ocupacion = 0;



        for($i=$fechaInicio; $i<=$fechaFin; $i+=86400){
            
        $fecha = date("Y-m-d", $i);
      

        $reservas = Reserva::whereHas('habitacion', function($query) use($propiedad_id){

                    $query->where('propiedad_id', $propiedad_id);

        })->where('checkin','<=' ,$fecha)->where('checkout', '>', $fecha)->where('estado_reserva_id' , 4)->get();

        $reservas_dia = count($reservas);

        $suma_ocupacion += $reservas_dia;

        }

        $ocupacion = ($suma_ocupacion * 100) / $mes;

        $ventas_totales = 0;
        foreach($total_reservas as $reserva){

          $ventas_totales += $reserva->monto_total;


        }

        /*ingreso por tipo de cliente */

        $ingreso_empresa = 0;
        $ingreso_particular = 0;

        $reserva_empresa = Reserva::whereHas('habitacion', function($query) use($propiedad_id){

            $query->where('propiedad_id', $propiedad_id);

        })->whereHas('cliente', function($query){

            $query->where('tipo_cliente_id', 2);

        })->whereBetween('checkin' , $rango)->where('estado_reserva_id' , 4)->get();


        foreach($reserva_empresa as $reserva){

          $ingreso_empresa += $reserva->monto_total;



        }

        $reserva_particular = Reserva::whereHas('habitacion', function($query) use($propiedad_id){

            $query->where('propiedad_id', $propiedad_id);

        })->whereHas('cliente', function($query){

            $query->where('tipo_cliente_id', 1);

        })->whereBetween('checkin' , $rango)->where('estado_reserva_id' , 4)->get();


        foreach($reserva_particular as $reserva){

          $ingreso_particular += $reserva->monto_total;



        }


        /*ingresos por tipo de fuente*/

        $reservas_web = Reserva::whereHas('habitacion', function($query) use($propiedad_id){

            $query->where('propiedad_id', $propiedad_id);

        })->whereBetween('checkin' , $rango)->where('estado_reserva_id' , 4)->where('tipo_fuente_id' , 1)->get();

        $reservas_caminando = Reserva::whereHas('habitacion', function($query) use($propiedad_id){

            $query->where('propiedad_id', $propiedad_id);

        })->whereBetween('checkin' , $rango)->where('estado_reserva_id' , 4)->where('tipo_fuente_id' , 2)->get();

        $reservas_telefono = Reserva::whereHas('habitacion', function($query) use($propiedad_id){

            $query->where('propiedad_id', $propiedad_id);

        })->whereBetween('checkin' , $rango)->where('estado_reserva_id' , 4)->where('tipo_fuente_id' , 3)->get();

        $reservas_email = Reserva::whereHas('habitacion', function($query) use($propiedad_id){

            $query->where('propiedad_id', $propiedad_id);

        })->whereBetween('checkin' , $rango)->where('estado_reserva_id' , 4)->where('tipo_fuente_id' , 4)->get();

        $reservas_sociales = Reserva::whereHas('habitacion', function($query) use($propiedad_id){

            $query->where('propiedad_id', $propiedad_id);

        })->whereBetween('checkin' , $rango)->where('estado_reserva_id' , 4)->where('tipo_fuente_id' , 5)->get();




            $data = array(

            'total_reservas'          => count($total_reservas),
            'ocupacion'               => round($ocupacion),
            'ventas_totales'          => round($ventas_totales),
            'ingreso_empresas'        => round($ingreso_empresa),
            'ingreso_particulares'    => round($ingreso_particular),
            'reservas_web'            => count($reservas_web),
            'reservas_caminando'      => count($reservas_caminando),
            'reservas_telefono'       => count($reservas_telefono),
            'reservas_email'          => count($reservas_email),
            'reservas_redes_sociales' => count($reservas_sociales),
            );

            return $data;


      }else{


            $retorno = array(

                'msj'    => "La solicitud esta incompleta",
                'errors' => true
            );

            return Response::json($retorno, 400);


      }





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

        $q->where('propiedad_id', $id)->with('tipoHabitacion');}])->get();


        $reservas = Reserva::whereHas('habitacion', function($query) use($id){

                    $query->where('propiedad_id', $id);

        })->with('habitacion.tipoHabitacion')->with('cliente','huespedes.servicios','tipoFuente', 'metodoPago','estadoReserva','pagos')->whereBetween('checkin', $fechas)->get();



        $reservas_checkout = Reserva::whereHas('habitacion', function($query) use($id){

                    $query->where('propiedad_id', $id);

        })->with('habitacion.tipoHabitacion')->with('cliente','huespedes','tipoFuente', 'metodoPago','estadoReserva')->whereBetween('checkout', $fechas)->get();



        $habitaciones_tipo = [];

        foreach ($tipos as $tipo) {
                
            $habitaciones = $tipo->habitaciones;
            $nombre_tipo = $tipo->nombre;

            $nombre = [ 'nombre' => $nombre_tipo, 'header' => 1];
            array_push($habitaciones_tipo, $nombre);

            foreach ($habitaciones as $habitacion) {
                
/*                $nombre_habitacion = $habitacion->nombre;
                $hab = [ 'nombre' => $nombre_habitacion];*/
                array_push($habitaciones_tipo, $habitacion);



            }


        }

        $reservas_calendario = [];
        $mes_calendario =date ("F",strtotime($fecha_inicio));
        foreach ($reservas as $reserva) {
       

            $checkin =date ("F",strtotime($reserva->checkin));
            $checkout =date ("F",strtotime($reserva->checkout));

            if($checkin == $checkout){

                $noches = $reserva->noches;
                $dia = date ("j",strtotime($reserva->checkin));
                $reserva->left = ($dia * $ancho_celdas)-30;

                $reserva->right = (($ancho_calendario - $reserva->left - ($noches * $ancho_celdas))+$ancho_celdas)-60;

                array_push($reservas_calendario, $reserva);
             }else{

                
            $mes_checkin =date ("F",strtotime($reserva->checkin));
            $mes_calendario =date ("F",strtotime($fecha_inicio));

            if ($mes_checkin == $mes_calendario) {


                $dia = date ("j",strtotime($reserva->checkin));
                $reserva->left = ($dia * $ancho_celdas)-30;

                $reserva->right = 0;

                } 


            array_push($reservas_calendario, $reserva);


            }



        }

            foreach ($reservas_checkout as $reserva_checkout) {
                $mes_checkout =date ("F",strtotime($reserva_checkout->checkout));
                $mes_checkin =date ("F",strtotime($reserva_checkout->checkin));
                $dia_checkout= date ("j",strtotime($reserva_checkout->checkout));
            
                 if ($mes_checkout == $mes_calendario) {
                     if ($mes_checkin != $mes_calendario) {

                    $reserva_checkout->left = 0;
                    $reserva_checkout->right = (($ancho_calendario - ( $ancho_celdas * $dia_checkout))+$ancho_celdas)-30;

                    array_push($reservas_calendario, $reserva_checkout);

                         }
                    }

                }

            array_push($calendario, $habitaciones_tipo);
            array_push($calendario, $reservas_calendario);

         return $calendario;




    }

    public function show($id){



       $reservas = Reserva::where('id', $id)->first();

       if(!is_null($reservas)){


        $reservas = Reserva::where('id', $id)->with('habitacion.tipoHabitacion')->with('cliente','huespedes.servicios','tipoFuente', 'metodoPago','estadoReserva','pagos.tipoComprobante')->get();

            foreach ($reservas as $reserva){
                foreach ($reserva['huespedes'] as $huesped) {
                    $huesped->consumo_total = 0;
                    foreach ($huesped['servicios'] as $servicio) {
                        $huesped->consumo_total += $servicio->pivot->precio_total;

                    }
                }

            }


            $reserva =  $reservas->first();
            return $reserva;


       }else{

            $data = array(

                    'msj' => "Reserva no encontrada",
                    'errors' => true


                );

            return Response::json($data, 404);



       }



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


    public function destroy($id){


        $reserva = Reserva::findOrFail($id);
        $reserva->delete();

        $data = [

            'errors' => false,
            'msg' => 'Reserva eliminada satisfactoriamente',

        ];

        return Response::json($data, 202);

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


    public function getTipoComprobante(){


        $TipoComprobante = TipoComprobante::all();

        $respuesta = [

        'Tipo_comprobante' => $TipoComprobante,

        ];


        return $respuesta;



    }



}


