<?php

namespace App\Http\Controllers;

use App\ClasificacionMoneda;
use App\Http\Controllers\Controller;
use App\PrecioServicio;
use App\Propiedad;
use App\PropiedadMoneda;
use App\Servicio;
use App\TipoHabitacion;
use App\TipoPropiedad;
use App\Pago;
use App\Reserva;
use App\Pais;
use App\Region;
use App\Huesped;
use App\PrecioTemporada;
use App\ZonaHoraria;
use App\TipoCobro;
use App\TipoComprobante;
use App\MetodoPago;
use App\TipoFuente;
use App\TipoCliente;
use Illuminate\Support\Facades\Config;
use Input;
use Illuminate\Http\Request;
use Response;
use Validator;
use \Carbon\Carbon;

class PropiedadController extends Controller
{
    /**
     * Reporte financiero de propiedad
     *
     * @author ALLEN
     *
     * @param  Request          $request (propiedad_id, $fecha_inicio, $fecha_fin)
     * @return Response::json
     */
    public function reporteFinanciero(Request $request)
    {
        if ($request->has('propiedad_id')) {
            $propiedad_id = $request->input('propiedad_id');
            $propiedad    = Propiedad::where('id', $propiedad_id)->first();
            if (is_null($propiedad)) {
                $retorno = array(
                    'msj'    => "Propiedad no encontrada",
                    'errors' => true);
                return Response::json($retorno, 404);
            }
        } else {
            $retorno = array(
                'msj'    => "No se envia propiedad_id",
                'errors' => true);
            return Response::json($retorno, 400);
        }

        if ($request->has('fecha_inicio')) {
            $getInicio       = new Carbon($request->input('fecha_inicio'));
            $inicio          = $getInicio->startOfDay();
            $zona_horaria    = ZonaHoraria::where('id', $propiedad->zona_horaria_id)->first();
            $pais            = $zona_horaria->nombre;
            $fecha_inicio    = Carbon::createFromFormat('Y-m-d H:i:s', $inicio, $pais)->tz('UTC');
        }

        if ($request->has('fecha_fin')) {
            $fin             = new Carbon($request->input('fecha_fin'));
            $fechaFin        = $fin->addDay();
            $fin_fecha       = $fechaFin->startOfDay();
            $fecha_fin       = Carbon::createFromFormat('Y-m-d H:i:s', $fechaFin, $pais)->tz('UTC');
        } else {
            $fecha_fin       = Carbon::createFromFormat('Y-m-d H:i:s', $inicio, $pais)->tz('UTC')->addDay();
        }

        $pagos    = Pago::where('created_at','>=' , $fecha_inicio)->where('created_at', '<' , $fecha_fin)
            ->whereHas('reserva.habitacion', function($query) use($propiedad_id){
                $query->where('propiedad_id', $propiedad_id);
        })->with(['tipoComprobante', 'metodoPago', 'tipoMoneda','reserva.habitacion.tipoHabitacion', 'reserva.cliente.tipoCliente', 'reserva.tipoFuente'])->get();

        $reservas = Reserva::whereHas('habitacion', function($query) use($propiedad_id){
                $query->where('propiedad_id', $propiedad_id);
        })->whereHas('pagos', function($query) use($fecha_inicio,$fecha_fin){
                $query->where('created_at','>=' , $fecha_inicio)->where('created_at', '<' , $fecha_fin);
        })->with(['habitacion.tipoHabitacion', 'tipoFuente'])->get();

        $propiedad_monedas         = $propiedad->tipoMonedas;
        $metodo_pagos              = MetodoPago::all();
        $ingresos_metodo_pago      = [];
        foreach ($metodo_pagos as $metodo) {
            $ingresos_moneda = [];
            foreach ($propiedad_monedas as $moneda) {
                $suma_ingreso   = 0;
                foreach ($pagos as $pago) {
                    if ($moneda->id == $pago->tipo_moneda_id) {
                        if ($metodo->nombre == $pago->MetodoPago->nombre) {
                            $suma_ingreso += $pago->monto_equivalente;
                        }
                    }
                }
                $ingresos['monto']                   = $suma_ingreso;
                $ingresos['tipo_moneda_id']          = $moneda->id;
                $ingresos['nombre_moneda']           = $moneda->nombre;
                $ingresos['cantidad_decimales']      = $moneda->cantidad_decimales;  
                array_push($ingresos_moneda, $ingresos);
            }
            $ingresos_pago['id']           = $metodo->id;
            $ingresos_pago['nombre']       = $metodo->nombre;
            $ingresos_pago['ingresos']     = $ingresos_moneda;
            array_push($ingresos_metodo_pago, $ingresos_pago);
 

        }

        $tipo_fuentes              = TipoFuente::all();
        $ingresos_tipo_fuente      = [];
        foreach ($tipo_fuentes as $fuente) {
            $cantidad = 0;
            foreach ($reservas as $reserva) {
                if ($reserva->tipo_fuente_id == $fuente->id) {
                    $cantidad++;
                }
            }
            $ingresos_moneda = [];
            foreach ($propiedad_monedas as $moneda) {
                $suma_ingreso   = 0;
                foreach ($pagos as $pago) {
                    if ($moneda->id == $pago->tipo_moneda_id) {
                        if ($fuente->nombre == $pago->reserva->tipoFuente->nombre) {
                            $suma_ingreso += $pago->monto_equivalente;
                        }
                    }
                }
                $ingresos['monto']                   = $suma_ingreso;
                $ingresos['tipo_moneda_id']          = $moneda->id;
                $ingresos['nombre_moneda']           = $moneda->nombre;
                $ingresos['cantidad_decimales']      = $moneda->cantidad_decimales;  
                array_push($ingresos_moneda, $ingresos);
            }
            $ingresos_fuente['id']           = $fuente->id;
            $ingresos_fuente['nombre']       = $fuente->nombre;
            $ingresos_fuente['cantidad']     = $cantidad;
            $ingresos_fuente['ingresos']     = $ingresos_moneda;
            array_push($ingresos_tipo_fuente, $ingresos_fuente);
        }

        $tipos_habitacion = TipoHabitacion::whereHas('habitaciones', function ($query) use ($propiedad_id) {
                $query->where('propiedad_id', $propiedad_id);
        })->get();

        $ingresos_tipo_habitacion   = [];
        foreach ($tipos_habitacion as $tipo) {
            $cantidad = 0;
            foreach ($reservas as $reserva) {
                if ($reserva->habitacion->tipoHabitacion->nombre == $tipo->nombre) {
                    $cantidad++;
                }
            }
            $ingresos_moneda = [];
            foreach ($propiedad_monedas as $moneda) {
                $suma_ingreso   = 0;
                foreach ($pagos as $pago) {
                    if ($moneda->id == $pago->tipo_moneda_id) {
                        if ($tipo->nombre == $pago->reserva->habitacion->tipoHabitacion->nombre) {
                            $suma_ingreso += $pago->monto_equivalente;
                        }
                    }
                }
                $ingresos['monto']                   = $suma_ingreso;
                $ingresos['tipo_moneda_id']          = $moneda->id;
                $ingresos['nombre_moneda']           = $moneda->nombre;
                $ingresos['cantidad_decimales']      = $moneda->cantidad_decimales;  
                array_push($ingresos_moneda, $ingresos);
            }
            $ingresos_habitacion['id']           = $tipo->id;
            $ingresos_habitacion['nombre']       = $tipo->nombre;
            $ingresos_habitacion['cantidad']     = $cantidad;
            $ingresos_habitacion['ingresos']     = $ingresos_moneda;
            array_push($ingresos_tipo_habitacion, $ingresos_habitacion);
        }

        $tipos_cliente           = TipoCliente::all();
        $ingresos_tipo_cliente   = [];
        foreach ($tipos_cliente as $tipo) {
            $ingresos_moneda = [];
            foreach ($propiedad_monedas as $moneda) {
                $suma_ingreso   = 0;
                foreach ($pagos as $pago) {
                    if ($moneda->id == $pago->tipo_moneda_id) {
                        if ($tipo->nombre == $pago->reserva->cliente->tipoCliente->nombre) {
                            $suma_ingreso += $pago->monto_equivalente;
                        }
                    }
                }
                $ingresos['monto']                   = $suma_ingreso;
                $ingresos['tipo_moneda_id']          = $moneda->id;
                $ingresos['nombre_moneda']           = $moneda->nombre;
                $ingresos['cantidad_decimales']      = $moneda->cantidad_decimales;  
                array_push($ingresos_moneda, $ingresos);
            }
            $ingresos_habitacion['id']           = $tipo->id;
            $ingresos_habitacion['nombre']       = $tipo->nombre;
            $ingresos_habitacion['ingresos']     = $ingresos_moneda;
            array_push($ingresos_tipo_cliente, $ingresos_habitacion);
        }

        $ingresos_total['tipos_habitaciones']    = $ingresos_tipo_habitacion;
        $ingresos_total['tipos_fuentes']         = $ingresos_tipo_fuente;
        $ingresos_total['tipos_clientes']        = $ingresos_tipo_cliente;
        $ingresos_total['metodos_pagos']         = $ingresos_metodo_pago;

        return $ingresos_total;
    }

    /**
     * Reporte financiero anual de propiedad
     *
     * @author ALLEN
     *
     * @param  Request          $request (propiedad_id, $ano_actua)
     * @return Response::json
     */
    public function reporteFinancieroAnual(Request $request)
    {
        if ($request->has('propiedad_id')) {
            $propiedad_id = $request->input('propiedad_id');
            $propiedad    = Propiedad::where('id', $propiedad_id)->first();
            if (is_null($propiedad)) {
                $retorno = array(
                    'msj'    => "Propiedad no encontrada",
                    'errors' => true);
                return Response::json($retorno, 404);
            }
        } else {
            $retorno = array(
                'msj'    => "No se envia propiedad_id",
                'errors' => true);
            return Response::json($retorno, 400);
        }

        if ($request->has('ano_actual')) {
            $ano_actual = $request->input('ano_actual');
        } else {
            $retorno = array(
               'msj'    => "No se envia año actual",
               'errors' => true);
            return Response::json($retorno, 404);
        }

        $moneda_propiedad = $propiedad->tipoMonedas;
        $cantidad_monedas = count($moneda_propiedad);
        $años             = Config::get('reportes.años');
        $meses            = Config::get('reportes.meses');
        $ingresos_mes     = [];

        foreach ($meses as $mes) {
            $mes_año = $mes;    
            foreach ($años as $año) {
                foreach ($año[$ano_actual] as $m) {
                    $fecha_inicio = $m[$mes_año]['inicio'];
                    $fecha_fin    = $m[$mes_año]['fin'];

                    if ($fecha_inicio) {
                        $getInicio       = new Carbon($fecha_inicio);
                        $inicio          = $getInicio->startOfDay();
                        $zona_horaria    = ZonaHoraria::where('id', $propiedad->zona_horaria_id)->first();
                        $pais            = $zona_horaria->nombre;
                        $fecha_inicio    = Carbon::createFromFormat('Y-m-d H:i:s', $inicio, $pais)->tz('UTC');
                    }

                    if ($fecha_fin) {
                        $fin             = new Carbon($fecha_fin);
                        $fechaFin        = $fin->addDay();
                        $fecha_fin       = Carbon::createFromFormat('Y-m-d H:i:s', $fechaFin, $pais)->tz('UTC');
                    }

                    $pagos = Pago::where('created_at','>=' , $fecha_inicio)->where('created_at', '<' , $fecha_fin)
                        ->whereHas('reserva.habitacion', function($query) use($propiedad_id){
                            $query->where('propiedad_id', $propiedad_id);
                    })->with('tipoComprobante', 'metodoPago', 'tipoMoneda')->with('reserva')->get();

                    $i = 1;
                    foreach ($moneda_propiedad as $moneda) {
                        $suma_pagos = 0;
                        foreach ($pagos as $pago) { 
                            if ($moneda->id == $pago->tipo_moneda_id) {
                                $suma_pagos += $pago->monto_equivalente;
                            }
                        }
                        $ingreso['moneda-'.$i]      = $moneda->nombre;
                        $ingreso['monto-'.$i]       = $suma_pagos;
                        $ingreso['mes']             = $mes_año;
                        $ingreso['fecha_inicio']    = $m[$mes_año]['inicio'];
                        $ingreso['fecha_fin']       = $m[$mes_año]['fin'];
                        $i++;
                    }
                    $montos = $ingreso;
                }
            }
            array_push($ingresos_mes, $montos);
        }

        $grafico =[ 'grafico_1' => $ingresos_mes];
        return $grafico;
    }

    public function reportes(Request $request){

        if ($request->has('propiedad_id')) {
            $propiedad_id = $request->input('propiedad_id');
            $propiedad    = Propiedad::where('id', $propiedad_id)->first();
            if (is_null($propiedad)) {
                $retorno = array(
                    'msj'    => "Propiedad no encontrada",
                    'errors' => true);
                return Response::json($retorno, 404);
            }
        } else {
            $retorno = array(
                'msj'    => "No se envia propiedad_id",
                'errors' => true);
            return Response::json($retorno, 400);
        }
        
        if ($request->has('fecha_inicio')) {
            $getInicio       = new Carbon($request->input('fecha_inicio'));
            $inicio          = $getInicio->startOfDay();
            $zona_horaria    = ZonaHoraria::where('id', $propiedad->zona_horaria_id)->first();
            $pais            = $zona_horaria->nombre;
            $fecha_inicio    = Carbon::createFromFormat('Y-m-d H:i:s', $inicio, $pais)->tz('UTC');
        }

        if ($request->has('fecha_fin')) {
            $fin             = new Carbon($request->input('fecha_fin'));

            $fechaFin        = $fin->addDay();
            $fin_fecha       = $fechaFin->startOfDay();

            $fecha_fin       = Carbon::createFromFormat('Y-m-d H:i:s', $fechaFin, $pais)->tz('UTC');
                
 

        } else {
            $fecha_fin       = Carbon::createFromFormat('Y-m-d H:i:s', $inicio, $pais)->tz('UTC')->addDay();
        }


        $pagos = Pago::where('created_at','>=' , $fecha_inicio)->where('created_at', '<' , $fecha_fin)
            ->whereHas('reserva.habitacion', function($query) use($propiedad_id){
                $query->where('propiedad_id', $propiedad_id);
        })->with('tipoComprobante', 'metodoPago', 'tipoMoneda')->with('reserva')->get();



        $reservas_creadas = Reserva::where('created_at' , '>=', $fecha_inicio)->where('created_at', '<' , $fecha_fin)->whereHas('habitacion', function($query) use($propiedad_id){
        $query->where('propiedad_id', $propiedad_id);
        })->get();

        $auxInicio = $getInicio->format('Y-m-d');
        $auxFin    = $fecha_fin->format('Y-m-d');

        $reservas = Reserva::whereHas('habitacion', function($query) use($propiedad_id){
            $query->where('propiedad_id', $propiedad_id);
        })->where(function ($query) use ($auxInicio, $auxFin) {
            $query->where(function ($query) use ($auxInicio, $auxFin) {
                $query->where('checkin', '>=', $auxInicio);
                $query->where('checkin', '<',  $auxFin);
                        });
            $query->orWhere(function($query) use ($auxInicio,$auxFin){
                $query->where('checkin', '<=', $auxInicio);
                $query->where('checkout', '>',  $auxInicio);
        });                
        })->with('huespedes.pais')->get();


        /* INGRESOS TOTALES DEL DIA  */

        $ingresos_totales_dia = [];
        $ingresos_habitacion = [];
        $ingresos_consumos = [];

       foreach ($propiedad->tipoMonedas as $moneda) {
          $tipo_moneda_id = $moneda->pivot->tipo_moneda_id;

          $pagos_tipo_moneda = $pagos->where('tipo_moneda_id', $tipo_moneda_id);

          $suma_pagos = 0;
          $ingresos_por_habitacion = 0;
          $ingresos_por_consumos = 0;

          foreach ($pagos_tipo_moneda as $pago) {

              $suma_pagos += $pago->monto_equivalente;

              if($pago->tipo == 'Pago habitacion'){

                $ingresos_por_habitacion += $pago->monto_equivalente;


              }elseif($pago->tipo == 'Pago consumos'){

                $ingresos_por_consumos += $pago->monto_equivalente;


              }elseif ($pago->tipo == 'Confirmacion de reserva') {
                $ingresos_por_habitacion += $pago->monto_equivalente;

              }

          }



          $ingresos = ['monto' => $suma_pagos , 'tipo_moneda_id' => $tipo_moneda_id, 'nombre_moneda' => $moneda->nombre, 'cantidad_decimales' => $moneda->cantidad_decimales]; 
          $ingresos_hab = ['monto' => $ingresos_por_habitacion,'tipo_moneda_id' => $tipo_moneda_id, 'nombre_moneda' => $moneda->nombre, 'cantidad_decimales' => $moneda->cantidad_decimales];
          $ingresos_serv = ['monto' => $ingresos_por_consumos,'tipo_moneda_id' => $tipo_moneda_id, 'nombre_moneda' => $moneda->nombre, 'cantidad_decimales' => $moneda->cantidad_decimales];


          array_push($ingresos_totales_dia, $ingresos);
          array_push($ingresos_habitacion, $ingresos_hab);
          array_push($ingresos_consumos, $ingresos_serv);


          
    }


         /*RESERVAS ANULADAS*/

        $reservas_anuladas = Reserva::where('updated_at' , '>=', $fecha_inicio)->where('updated_at', '<' , $fecha_fin)->whereHas('habitacion', function($query) use($propiedad_id){

        $query->where('propiedad_id', $propiedad_id);

        })->where('estado_reserva_id', 6)->get();

        /*RESERVAS NO SHOW*/

        $reservas_no_show = Reserva::where('updated_at' , '>=', $fecha_inicio)->where('updated_at', '<' , $fecha_fin)->whereHas('habitacion', function($query) use($propiedad_id){

        $query->where('propiedad_id', $propiedad_id);

        })->where('estado_reserva_id', 7)->get();


        /*PAISES*/

        $paises = [];
        foreach ($reservas as $reserva) {
            foreach ($reserva['huespedes'] as $huesped) {

                    $pais = $huesped->pais;
                    if (!is_null($pais)) {
                        $pais_id = $huesped->pais->id;
                        $propiead_pais_id = $propiedad->pais->id;
                        
                        if ($pais_id != $propiead_pais_id ) {
                            if ($huesped->pais != null && !in_array($pais, $paises) ) {
                                array_push($paises, $pais);
                            }
                            
                        }
                        
                    }



            }       

        }


       $residentes_extranjero = [];
       
       foreach ($paises as $pais) {
            $huespedes = 0;
            $noches    = 0;
            foreach ($reservas as $reserva) {
                foreach ($reserva->huespedes as $huesped) {
                    if ($pais->id == $huesped->pais_id) {
                        $huespedes++;
                        $noches += $reserva->noches;

                    }

                }

            }
            
            $extranjeros = [ 'nombre' => $pais->nombre, 'llegadas' => $huespedes, 'pernoctacion' => $noches];
            array_push($residentes_extranjero, $extranjeros);

       }


     /* REGIONES*/


        $regiones = Region::where('pais_id', $propiedad->pais_id)->get();

        $residentes_pais_propiedad = [];

        foreach ($regiones as $region) {
            
            $huespedes = 0;
            $noches    = 0;
            foreach ($reservas as $reserva) {
                foreach ($reserva->huespedes as $huesped) {
                    if ($region->id == $huesped->region_id) {
                        $huespedes++;
                        $noches += $reserva->noches;

                    }

                }

            }
            
            $residentes_pais = [ 'nombre' => $region->nombre, 'llegadas' => $huespedes, 'pernoctacion' => $noches];
            array_push($residentes_pais_propiedad, $residentes_pais);


        }


        /*GRAFICO*/

       $cantidad_noches  = $fecha_inicio->diffInDays($fecha_fin); 


       $auxFecha_inicio  = new Carbon($auxInicio);
       $auxFecha_fin     = new Carbon($auxFin);
       $suma             = 0;
        while ($auxFecha_inicio < $auxFecha_fin) {
            $fecha = $auxFecha_inicio->format('Y-m-d');

            foreach ($reservas as $reserva) {
                
                if ($reserva->checkin <= $fecha && $reserva->checkout > $fecha) {
                        
                    if ($reserva->estado_reserva_id == 3 || $reserva->estado_reserva_id == 4 || $reserva->estado_reserva_id == 5) {
                        
                        $suma++;
                    }


                }


            }


         $auxFecha_inicio->addDay();
        }
        
        $cantidad_habitaciones = count($propiedad->habitaciones);
        $total_noches = $cantidad_habitaciones * $cantidad_noches;

        $grafico = [['nombre' => 'Ocupado','valor' => $suma],['nombre' => 'Disponible', 'valor' => ($total_noches - $suma)]];


      $data = [ 
                'ingresos_totales'          => $ingresos_totales_dia,
                'reservas_realizadas'       => count($reservas_creadas),
                'reservas_anuladas'         => count($reservas_anuladas),
                'reservas_no_show'          => count($reservas_no_show),
                'ingresos_por_habitacion'   => $ingresos_habitacion,
                'ingresos_por_servicios'    => $ingresos_consumos,
                'residentes'                => [['nombre' => 'Locales' , 'regiones' => $residentes_pais_propiedad], ['nombre' => 'Extranjeros' , 'paises' => $residentes_extranjero]],
                'grafico'                   => $grafico
                        
                ]; 


    return $data;



    } //fin metodo reportesMensual



    public function pagos(Request $request)
    {
        if ($request->has('propiedad_id')) {
            $propiedad_id = $request->input('propiedad_id');
            $propiedad    = Propiedad::where('id', $propiedad_id)->first();
            if (is_null($propiedad)) {
                $retorno = array(
                    'msj'    => "Propiedad no encontrada",
                    'errors' => true);
                return Response::json($retorno, 404);
            }
        } else {
            $retorno = array(
                'msj'    => "No se envia propiedad_id",
                'errors' => true);
            return Response::json($retorno, 400);
        }
        
        if ($request->has('fecha_inicio')) {
            $getInicio       = new Carbon($request->input('fecha_inicio'));
            $inicio          = $getInicio->startOfDay();
            $zona_horaria    = ZonaHoraria::where('id', $propiedad->zona_horaria_id)->first();
            $pais            = $zona_horaria->nombre;
            $fecha_inicio    = Carbon::createFromFormat('Y-m-d H:i:s', $inicio, $pais)->tz('UTC');
        }

        if ($request->has('fecha_fin')) {
            $fin             = new Carbon($request->input('fecha_fin'));
            $fechaFin        = $fin->addDay();
            $fecha_fin       = Carbon::createFromFormat('Y-m-d H:i:s', $fechaFin, $pais)->tz('UTC');
        } else {
            $fecha_fin       = Carbon::createFromFormat('Y-m-d H:i:s', $inicio, $pais)->tz('UTC')->addDay();
        }

        $pagos = Pago::where('created_at','>=' , $fecha_inicio)->where('created_at', '<' , $fecha_fin)
            ->whereHas('reserva.habitacion', function($query) use($propiedad_id){
                $query->where('propiedad_id', $propiedad_id);
        })->with('tipoComprobante', 'metodoPago', 'tipoMoneda')->with('reserva')->get();

        $moneda_propiedad = $propiedad->tipoMonedas;
        $auxInicio      = new Carbon($inicio);
        $auxFecha_fin   = new Carbon($fecha_fin);
        $auxFin         = $auxFecha_fin->startOfDay();

        $fechas_pagos = [];
        while ($auxInicio < $auxFin) {
            $auxFecha_inicio = $auxInicio->format('Y-m-d');
            $auxPagos = [];
            foreach ($pagos as $pago) {
                $created_at     = new Carbon($pago->created_at);
                $auxCreated_at  = $created_at->format('Y-m-d');
                if ($auxCreated_at == $auxFecha_inicio ) {
                    array_push($auxPagos, $pago);
                }
            }

            $auxMoneda = [];
            foreach ($moneda_propiedad as $moneda) {
                $moneda_id = $moneda->id;
                $suma_pago = 0;
                foreach ($auxPagos as $pago) {
                    $tipo_moneda_pago = $pago->tipo_moneda_id;
                    if ($moneda_id == $tipo_moneda_pago) {
                        $suma_pago += $pago->monto_equivalente;
                    }
                }
                $ingreso['nombre_moneda']       = $moneda->nombre;
                $ingreso['monto']               = $suma_pago;
                $ingreso['tipo_moneda_id']      = $moneda->pivot->tipo_moneda_id;
                $ingreso['cantidad_decimales']  = $moneda->cantidad_decimales;
                array_push($auxMoneda, $ingreso);
            }

        $data['fecha']     = $auxFecha_inicio;
        $data['ingresos']  = $auxMoneda;
        $data['pagos']     = $auxPagos;
        if (count($auxPagos) != 0) {
            array_push($fechas_pagos, $data);
        }
        $data = [];
        $auxInicio->addDay();
        }

        return $fechas_pagos;
    }






    public function ingresoServicio(Request $request)
    {

        if ($request->has('venta_servicio') && $request->has('propiedad_id') && $request->has('metodo_pago_id')) {

            $propiedad           = Propiedad::where('id', $request->input('propiedad_id'))->first();
            $metodo_pago_id      = $request->input('metodo_pago_id');
            $numero_operacion    = $request->input('numero_operacion');
            $tipo_comprobante_id = $request->input('tipo_comprobante_id');
            $numero_cheque       = $request->input('numero_cheque');

            if (!is_null($propiedad)) {

                $servicios = $request->input('venta_servicio');

                foreach ($servicios as $servicio) {

                    $servicio_id  = $servicio['servicio_id'];
                    $cantidad     = $servicio['cantidad'];
                    $precio_total = $servicio['precio_total'];

                    $serv                = Servicio::where('id', $servicio_id)->where('propiedad_id', $request->input('propiedad_id'))->first();
                    $cantidad_disponible = $serv->cantidad_disponible;

                    if (!is_null($serv)) {

                        if ($serv->categoria_id == 2) {

                            if ($cantidad >= 1) {

                                if ($serv->cantidad_disponible > 0) {

                                    if ($cantidad <= $serv->cantidad_disponible) {

                                        $servicio_id     = $serv->id;
                                        $servicio_nombre = $serv->nombre;

                                        $cantidad_disponible = $cantidad_disponible - $cantidad;

                                        $serv->update(array('cantidad_disponible' => $cantidad_disponible));

                                        $propiedad->vendeServicios()->attach($servicio_id, ['metodo_pago_id' => $metodo_pago_id, 'cantidad' => $cantidad, 'precio_total' => $precio_total, 'numero_operacion' => $numero_operacion, 'tipo_comprobante_id' => $tipo_comprobante_id, 'numero_cheque' => $numero_cheque]);

                                    } else {

                                        $data = array(

                                            'msj'    => " La cantidad ingresada es mayor al stock del producto",
                                            'errors' => true,

                                        );

                                        return Response::json($data, 400);

                                    }

                                } else {

                                    $data = array(

                                        'msj'    => " El servicio no tiene stock",
                                        'errors' => true,

                                    );

                                    return Response::json($data, 400);

                                }

                            } else {

                                $data = array(

                                    'msj'    => " La cantidad ingresada no corresponde",
                                    'errors' => true,

                                );

                                return Response::json($data, 400);

                            }

                        } elseif ($serv->categoria_id == 1) {

                            $propiedad->vendeServicios()->attach($servicio_id, ['metodo_pago_id' => $metodo_pago_id, 'cantidad' => $cantidad, 'precio_total' => $precio_total, 'numero_operacion' => $numero_operacion, 'tipo_comprobante_id' => $tipo_comprobante_id, 'numero_cheque' => $numero_cheque]);

                        }

                    } else {

                        $retorno = array(

                            'msj'    => "El servicio no pertenece a la propiedad",
                            'errors' => true,
                        );

                        return Response::json($retorno, 400);

                    }

                }

                $retorno = array(

                    'msj'   => "Servicios ingresados correctamente",
                    'erros' => false,
                );

                return Response::json($retorno, 201);

            } else {

                $data = array(

                    'msj'    => "Propiedad no encontrada",
                    'errors' => true,

                );

                return Response::json($data, 404);

            }

        } else {

            $retorno = array(

                'msj'    => "La solicitud esta incompleta",
                'errors' => true,
            );

            return Response::json($retorno, 400);

        }

    }

    public function index(Request $request)
    {

        if ($request->has('id')) {

            $propiedad = Propiedad::where('id', $request->input('id'))->with('tipoPropiedad')->with('tipoMonedas.clasificacionMonedas')->get();
            return $propiedad;

        }

    }

    public function show($id)
    {

        try {

            $propiedad = Propiedad::where('id', $id)->get();

            $tipos = TipoHabitacion::whereHas('habitaciones', function ($query) use ($id) {

                $query->where('propiedad_id', $id);

            })->get();

            foreach ($propiedad as $prop) {

                $prop->tipos_habitaciones = count($tipos);

            }

            return $propiedad;

        } catch (ModelNotFoundException $e) {
            $data = [
                'errors' => true,
                'msg'    => $e->getMessage(),
            ];
            return Response::json($data, 404);
        }

    }

    public function update(Request $request, $id)
    {

        $rules = array(

            'nombre'              => '',
            'tipo_propiedad_id'   => 'numeric',
            'numero_habitaciones' => 'numeric',
            'ciudad'              => '',
            'direccion'           => '',
            'telefono'            => '',
            'email'               => '',
            'nombre_responsable'  => '',
            'descripcion'         => '',
            'iva'                 => 'numeric',
            'porcentaje_deposito' => 'numeric',
            'pais_id'             => 'numeric',
            'region_id'           => 'numeric',
            'tipo_cobro_id'       => 'numeric',
            'zona_horaria_id'     => 'numeric',

        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            $data = [

                'errors' => true,
                'msg'    => $validator->messages(),

            ];

            return Response::json($data, 400);

        } else {

            $propiedad = Propiedad::findOrFail($id);

            $moneda_propiedad       = $propiedad->tipoMonedas;
            $tipos_habitacion       = $propiedad->tiposHabitacion;
            $temporadas_propiedad   = $propiedad->temporadas;

            if (count($tipos_habitacion) != 0 && count($temporadas_propiedad) != 0) {
                if ($request->has('tipo_cobro_id')) {

                    foreach ($tipos_habitacion as $tipo) {
                        foreach ($tipo['precios'] as $precio) {
                            $id                      = $precio->id;
                            $precio_tipo_habitacion  = PrecioTemporada::findOrFail($id);
                            $precio_tipo_habitacion->delete();
                        }
                        
                    }
                    
                    if ($request->input('tipo_cobro_id') != 3) {

                        foreach ($tipos_habitacion as $tipo) {
                            foreach ($temporadas_propiedad as $temporada) {
                                foreach ($moneda_propiedad as $moneda) {
                                    $precio_temporada                     = new PrecioTemporada();

                                    $precio_temporada->cantidad_huespedes = 1;
                                    $precio_temporada->precio             = 0;
                                    $precio_temporada->tipo_habitacion_id = $tipo->id;
                                    $precio_temporada->tipo_moneda_id     = $moneda->id;
                                    $precio_temporada->temporada_id       = $temporada->id;
                                    $precio_temporada->save();
                                }
                            }
                        }
                    }else{

                        foreach ($tipos_habitacion as $tipo) {
                            $capacidad = $tipo->capacidad;
                            foreach ($temporadas_propiedad as $temporada) {
                                foreach ($moneda_propiedad as $moneda) {

                                    for ($i=1; $i <= $capacidad  ; $i++) {
                                        $precio_temporada                     = new PrecioTemporada();

                                        $precio_temporada->cantidad_huespedes = $i;
                                        $precio_temporada->precio             = 0;
                                        $precio_temporada->tipo_habitacion_id = $tipo->id;
                                        $precio_temporada->tipo_moneda_id     = $moneda->id;
                                        $precio_temporada->temporada_id       = $temporada->id;
                                        $precio_temporada->save();   
                                    }
                                }
                            }
                        }
                    }
                }
            }


            $propiedad->update($request->all());
            $propiedad->touch();



            $data = [

                'errors' => false,
                'msg'    => 'Propiedad actualizada satisfactoriamente',

            ];

            return Response::json($data, 201);

        }

    }

    public function ingresoMonedas(Request $request)
    {

        if ($request->has('propiedad_id') && $request->has('monedas')) {

            $propiedad             = Propiedad::where('id', $request->input('propiedad_id'))->first();
            $tipos_habitacion      = $propiedad->tiposHabitacion;
            $servicios             = $propiedad->servicios;
            $temporadas            = $propiedad->temporadas;

            if (!is_null($propiedad)) {

                $monedas = $request->input('monedas');

                foreach ($monedas as $moneda) {
                    $clasificacion_moneda = $moneda['clasificacion_moneda_id'];
                    $tipo_moneda          = $moneda['tipo_moneda_id'];

                    $propiedad->clasificacionMonedas()->attach($clasificacion_moneda, ['tipo_moneda_id' => $tipo_moneda]);

                    if (count($tipos_habitacion) > 0) {
                        if ($propiedad->tipo_cobro_id != 3) {
                            foreach ($tipos_habitacion as $tipo) {
                                foreach ($temporadas as $temporada) {
                                    $precio_temporada                     = new PrecioTemporada();
                                    $precio_temporada->cantidad_huespedes = 1;
                                    $precio_temporada->precio             = 0;
                                    $precio_temporada->tipo_habitacion_id = $tipo->id;
                                    $precio_temporada->tipo_moneda_id     = $tipo_moneda;
                                    $precio_temporada->temporada_id       = $temporada->id;
                                    $precio_temporada->save();
                                }
                            }
                            
                        }else{

                            foreach ($tipos_habitacion as $tipo) {
                                $capacidad = $tipo->capacidad;
                                foreach ($temporadas as $temporada) {
                                    for ($i=1; $i <= $capacidad  ; $i++) {

                                        $precio_temporada                     = new PrecioTemporada();
                                        $precio_temporada->cantidad_huespedes = $i;
                                        $precio_temporada->precio             = 0;
                                        $precio_temporada->tipo_habitacion_id = $tipo->id;
                                        $precio_temporada->tipo_moneda_id     = $tipo_moneda;
                                        $precio_temporada->temporada_id       = $temporada->id;
                                        $precio_temporada->save();   
                                    }
                                }
                            }
                        }
                            
                    }

                    if (count($servicios) > 0) {

                        foreach ($servicios as $servicio) {

                            $servicio_id = $servicio->id;

                            $precio                  = new PrecioServicio();
                            $precio->precio_servicio = null;
                            $precio->tipo_moneda_id  = $tipo_moneda;
                            $precio->servicio_id     = $servicio_id;
                            $precio->save();

                            $servicio->update(array('estado_servicio_id' => 2));

                        }

                    }

                }

                $retorno = array(

                    'msj'   => "Moneda ingresada correctamente",
                    'erros' => false,
                );

                return Response::json($retorno, 201);

            } else {

                $retorno = array(

                    'msj'    => "Propiedad no encontrada",
                    'errors' => true,

                );

                return Response::json($retorno, 404);

            }

        } else {

            $retorno = array(

                'msj'    => "La solicitud esta incompleta",
                'errors' => true,
            );

            return Response::json($retorno, 400);

        }

    }

    public function eliminarMoneda(Request $request)
    {
        if ($request->has('moneda_id')) {
            $moneda_id  = $request->input('moneda_id');
            $moneda     = PropiedadMoneda::where('id', $moneda_id)->first();
            if (is_null($moneda)) {
                $retorno  = array(
                    'msj'    => "Moneda de propiedad no encontrada",
                    'errors' => true);
                return Response::json($retorno, 404);
            }

        } else {
            $retorno = array(
                'msj'    => "No se envia moneda_id",
                'errors' => true);
            return Response::json($retorno, 400);
        }
        
        $tipo_moneda_id = $moneda->tipo_moneda_id;
        $propiedad_id   = $moneda->propiedad_id;

        $precios_habitacion = PrecioTemporada::whereHas('temporada', function($query) use($propiedad_id){
            $query->where('propiedad_id', $propiedad_id);
        })->where('tipo_moneda_id', $tipo_moneda_id)->get();

        foreach ($precios_habitacion as $precio) {
            $precio->delete();
        }

        $precios_servicio = PrecioServicio::where('tipo_moneda_id', $tipo_moneda_id)->whereHas('servicio', function ($query) use ($propiedad_id) {
            $query->where('propiedad_id', $propiedad_id);
        })->get();

        foreach ($precios_servicio as $precio) {
            $precio->delete();
        }

        $moneda->delete();

        $retorno = array(
            'errors' => false,
            'msj'    => 'Moneda eliminada satisfactoriamente',
        );
        return Response::json($retorno, 202);

    }

    public function editarMoneda(Request $request, $id)
    {

        $rules = array(
            'clasificacion_moneda_id' => 'numeric',
            'tipo_moneda_id'          => 'numeric',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            $data = [

                'errors' => true,
                'msg'    => $validator->messages(),

            ];

            return Response::json($data, 400);

        } else {

            $moneda         = PropiedadMoneda::findOrFail($id);
            $tipo_moneda    = $moneda->tipo_moneda_id;

            $propiedad_id   = $moneda->propiedad_id;
            $tipo_moneda_id = $request->input('tipo_moneda_id');

            $precios_habitacion = PrecioTemporada::whereHas('temporada', function($query) use($propiedad_id){
                $query->where('propiedad_id', $propiedad_id);
            })->where('tipo_moneda_id', $tipo_moneda)->get();

            $precios_servicio = PrecioServicio::where('tipo_moneda_id', $tipo_moneda)->whereHas('servicio', function ($query) use ($propiedad_id) {
                $query->where('propiedad_id', $propiedad_id);
            })->get();

            if($tipo_moneda != $tipo_moneda_id){
                foreach ($precios_habitacion as $precio) {
                  $precio->update(array('precio' => 0, 'tipo_moneda_id' => $tipo_moneda_id));
                }
                foreach ($precios_servicio as $precio) {
                 $precio->update(array('precio_servicio' => null, 'tipo_moneda_id' => $tipo_moneda_id));
                 $servicio = $precio->servicio;
                 $servicio->update(array('estado_servicio_id' => 2));
                }
            }
            $moneda->update($request->all());
            $moneda->touch();

            $data = [
                'errors' => false,
                'msg'    => 'Moneda actualizada satisfactoriamente',
            ];
            return Response::json($data, 201);

        }
    }


    public function getTipoPropiedad()
    {

        $TipoPropiedad = TipoPropiedad::all();
        return $TipoPropiedad;
    }

    public function getClasificacionMoneda()
    {

        $clasificacion = ClasificacionMoneda::all();
        return $clasificacion;

    }

    public function getPaises(){

        $paises = Pais::all();

        return $paises;


    }

    public function getRegiones(Request $request){

        $pais_id = $request->input('pais_id');

        $regiones = Region::where('pais_id', $pais_id)->get();

        return $regiones;


    }

    public function getZonasHorarias()
    {

        $zonas = ZonaHoraria::all();

        return $zonas;


    }

    public function getTipoCobro()
    {
        $tipoCobros = TipoCobro::all();
        return $tipoCobros;


    }

    public function crearPais(Request $request)
    {

        foreach($request['countries'] as $countrie){

            $country = $countrie['country'];

            $pais             = new Pais();
            $pais->nombre     = $countrie['country'];
            $pais->save();

            foreach ($countrie['states'] as $state) {
                
                $region             = new Region();
                $region->nombre     = $state;
                $region->pais_id    = $pais->id;
                $region->save();

            }


        }

        return "paises creados";

    }

    public function crearZona(Request $request)
    {


        foreach ($request['zonas_horarias'] as $zona) {

            $zona_horaria               = new ZonaHoraria();
            $zona_horaria->nombre       = $zona;
            $zona_horaria->save();


        }

        return "zonas horarias creadas";



    }


}
