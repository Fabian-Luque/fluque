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
use App\EgresoCaja;
use App\EgresoPropiedad;
use App\Egreso;
use App\Politica;
use App\CuentaBancaria;
use App\TipoCuenta;
use App\PropiedadTipoDeposito;
use App\TipoDeposito;
use App\PropiedadServicio;
use App\Caja;
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
        })->with(['tipoComprobante', 'metodoPago', 'tipoMoneda','reserva.habitacion.tipoHabitacion', 'reserva.cliente.tipoCliente', 'reserva.tipoFuente', 'reserva.huespedes.servicios'])->get();

        $reservas = Reserva::whereHas('habitacion', function($query) use($propiedad_id){
                $query->where('propiedad_id', $propiedad_id);
        })->whereHas('pagos', function($query) use($fecha_inicio,$fecha_fin){
                $query->where('created_at','>=' , $fecha_inicio)->where('created_at', '<' , $fecha_fin);
        })->with(['habitacion.tipoHabitacion', 'tipoFuente', 'huespedes.servicios'])->get();

        $consumos = PropiedadServicio::where('propiedad_id', $propiedad_id)
        ->where('created_at','>=' , $fecha_inicio)
        ->where('created_at', '<' , $fecha_fin)
        ->with('servicio')
        ->get();

        $propiedad_monedas    = $propiedad->tipoMonedas;
        $total_habitacion     = [];
        $total_consumos       = [];
        $total_ingresos       = [];
        foreach ($propiedad_monedas as $moneda) {
            $tipo_moneda_id    = $moneda->id;
            $pagos_tipo_moneda = $pagos->where('tipo_moneda_id', $tipo_moneda_id);
            $suma_pagos              = 0;
            $ingresos_por_habitacion = 0;
            $ingresos_por_consumos   = 0;

            foreach ($consumos as $consumo) {
                if ($moneda->id == $consumo->tipo_moneda_id) {
                    $ingresos_por_consumos += $consumo->precio_total;
                    $suma_pagos += $consumo->precio_total;
                }
            }
            foreach ($pagos_tipo_moneda as $pago) {
                if ($pago->estado == 1) {
                    $suma_pagos += $pago->monto_equivalente;
                    if($pago->tipo == 'Pago habitacion'){
                        $ingresos_por_habitacion += $pago->monto_equivalente;
                    }elseif($pago->tipo == 'Pago consumos'){
                        $ingresos_por_consumos += $pago->monto_equivalente;
                    }elseif ($pago->tipo == 'Confirmacion de reserva') {
                        $ingresos_por_habitacion += $pago->monto_equivalente;
                    }
                }
            }

            $ingreso_total['monto']              = $suma_pagos;
            $ingreso_total['tipo_moneda_id']     = $tipo_moneda_id;
            $ingreso_total['nombre_moneda']      = $moneda->nombre;
            $ingreso_total['cantidad_decimales'] = $moneda->cantidad_decimales;

            $hab['monto']              = $ingresos_por_habitacion;
            $hab['tipo_moneda_id']     = $tipo_moneda_id;
            $hab['nombre_moneda']      = $moneda->nombre;
            $hab['cantidad_decimales'] = $moneda->cantidad_decimales;

            $serv['monto']              = $ingresos_por_consumos;
            $serv['tipo_moneda_id']     = $tipo_moneda_id;
            $serv['nombre_moneda']      = $moneda->nombre;
            $serv['cantidad_decimales'] = $moneda->cantidad_decimales;

            array_push($total_ingresos, $ingreso_total);
            array_push($total_habitacion, $hab);
            array_push($total_consumos, $serv);
        }

        $propiedad_monedas         = $propiedad->tipoMonedas;
        $metodo_pagos              = MetodoPago::all();
        $ingresos_metodo_pago      = [];
        foreach ($metodo_pagos as $metodo) {
            $ingresos_moneda = [];
            foreach ($propiedad_monedas as $moneda) {
                $suma_ingreso   = 0;
                foreach ($pagos as $pago) {
                    if ($pago->estado == 1) {
                        if ($moneda->id == $pago->tipo_moneda_id) {
                            if ($metodo->nombre == $pago->MetodoPago->nombre) {
                                $suma_ingreso += $pago->monto_equivalente;
                            }
                        }
                    }
                }
                foreach ($consumos  as $consumo) {
                    if ($moneda->id == $consumo->tipo_moneda_id) {
                        if ($metodo->id == $consumo->metodo_pago_id) {
                            $suma_ingreso += $consumo->precio_total;
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
                    if ($pago->estado == 1) {
                        if ($moneda->id == $pago->tipo_moneda_id) {
                            if ($fuente->nombre == $pago->reserva->tipoFuente->nombre) {
                                $suma_ingreso += $pago->monto_equivalente;
                            }
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
                    if ($pago->estado == 1) {
                        if ($moneda->id == $pago->tipo_moneda_id) {
                            if ($tipo->nombre == $pago->reserva->habitacion->tipoHabitacion->nombre) {
                                $suma_ingreso += $pago->monto_equivalente;
                            }
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
                    if ($pago->estado == 1) {
                        if ($moneda->id == $pago->tipo_moneda_id) {
                            if ($tipo->nombre == $pago->reserva->cliente->tipoCliente->nombre) {
                                $suma_ingreso += $pago->monto_equivalente;
                            }
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

        $servicios = Servicio::where('propiedad_id', $propiedad_id)->get();

        $cantidad_servicios = [];
        $servicios_vendidos = [];
        foreach ($servicios as $servicio) {
        $cantidad_vendido_huespedes   = 0;
        $cantidad_vendido_particulares = 0;
            foreach ($pagos as $pago) {
                if ($pago->estado == 1) {
                    foreach ($pago->reserva->huespedes as $huesped) {
                        foreach ($huesped->servicios as $serv) {
                            if ($servicio->nombre == $serv->nombre) {
                                $id = $serv->pivot->id;
                                if (!in_array($id, $servicios_vendidos)) {
                                    if ($serv->pivot->estado == "Pagado") {
                                        $cantidad_vendido_huespedes += $serv->pivot->cantidad;
                                        array_push($servicios_vendidos, $id);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            foreach ($consumos  as $consumo) {
                if ($servicio->id == $consumo->servicio_id) {
                    $cantidad_vendido_particulares += $consumo->cantidad;
                }
            }


            $cantidad_serv['id']       = $servicio->id;
            $cantidad_serv['nombre']   = $servicio->nombre;
            $cantidad_serv['cantidad_vendido_huespedes']    = $cantidad_vendido_huespedes;
            $cantidad_serv['cantidad_vendido_particulares'] = $cantidad_vendido_particulares;
            array_push($cantidad_servicios, $cantidad_serv);
        }

        $ingresos_total['ingresos_totales']         = $total_ingresos;
        $ingresos_total['ingresos_por_habitacion']  = $total_habitacion;
        $ingresos_total['ingresos_por_consumos']    = $total_consumos;
        $ingresos_total['cantidad_servicios']       = $cantidad_servicios;
        $ingresos_total['tipos_habitaciones']       = $ingresos_tipo_habitacion;
        $ingresos_total['tipos_fuentes']            = $ingresos_tipo_fuente;
        $ingresos_total['tipos_clientes']           = $ingresos_tipo_cliente;
        $ingresos_total['metodos_pagos']            = $ingresos_metodo_pago;

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
                            if ($pago->estado == 1) {
                                if ($moneda->id == $pago->tipo_moneda_id) {
                                    $suma_pagos += $pago->monto_equivalente;
                                }
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

    /**
     * Reporte egresos anual de propiedad
     *
     * @author ALLEN
     *
     * @param  Request          $request (propiedad_id, $ano_actua)
     * @return Response::json
     */
    public function reporteEgresoAnual(Request $request)
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
            return Response::json($retorno, 400);
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

                    $egresos_caja = EgresoCaja::whereHas('caja', function($query) use($propiedad_id){
                            $query->where('propiedad_id', $propiedad_id);
                    })->where('created_at', '>=' , $fecha_inicio)->where('created_at', '<' , $fecha_fin)->get();

                    $egresos_propiedad = EgresoPropiedad::where('propiedad_id', $propiedad_id)->where('created_at', '>=' , $fecha_inicio)->where('created_at', '<' , $fecha_fin)->get();

                    $i = 1;
                    foreach ($moneda_propiedad as $moneda) {
                        $suma_egresos = 0;
                        foreach ($egresos_caja as $egreso) { 
                            if ($moneda->id == $egreso->tipo_moneda_id) {
                                $suma_egresos += $egreso->monto;
                            }
                        }
                        foreach ($egresos_propiedad as $egreso) { 
                            if ($moneda->id == $egreso->tipo_moneda_id) {
                                $suma_egresos += $egreso->monto;
                            }
                        }
                        $ingreso['moneda-'.$i]      = $moneda->nombre;
                        $ingreso['monto-'.$i]       = $suma_egresos;
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


    // public function reporteConsumoParticularesAnual(Request $request)
    // {
    //     if ($request->has('propiedad_id')) {
    //         $propiedad_id = $request->input('propiedad_id');
    //         $propiedad    = Propiedad::where('id', $propiedad_id)->first();
    //         if (is_null($propiedad)) {
    //             $retorno = array(
    //                 'msj'    => "Propiedad no encontrada",
    //                 'errors' => true);
    //             return Response::json($retorno, 404);
    //         }
    //     } else {
    //         $retorno = array(
    //             'msj'    => "No se envia propiedad_id",
    //             'errors' => true);
    //         return Response::json($retorno, 400);
    //     }

    //     if ($request->has('ano_actual')) {
    //         $ano_actual = $request->input('ano_actual');
    //     } else {
    //         $retorno = array(
    //            'msj'    => "No se envia año actual",
    //            'errors' => true);
    //         return Response::json($retorno, 400);
    //     }

    //     $moneda_propiedad = $propiedad->tipoMonedas;
    //     $cantidad_monedas = count($moneda_propiedad);
    //     $años             = Config::get('reportes.años');
    //     $meses            = Config::get('reportes.meses');
    //     $aux_consumos     = [];

    //     foreach ($meses as $mes) {
    //         $mes_año = $mes;    
    //         foreach ($años as $año) {
    //             foreach ($año[$ano_actual] as $m) {
    //                 $fecha_inicio = $m[$mes_año]['inicio'];
    //                 $fecha_fin    = $m[$mes_año]['fin'];

    //                 if ($fecha_inicio) {
    //                     $getInicio       = new Carbon($fecha_inicio);
    //                     $inicio          = $getInicio->startOfDay();
    //                     $zona_horaria    = ZonaHoraria::where('id', $propiedad->zona_horaria_id)->first();
    //                     $pais            = $zona_horaria->nombre;
    //                     $fecha_inicio    = Carbon::createFromFormat('Y-m-d H:i:s', $inicio, $pais)->tz('UTC');
    //                 }

    //                 if ($fecha_fin) {
    //                     $fin             = new Carbon($fecha_fin);
    //                     $fechaFin        = $fin->addDay();
    //                     $fecha_fin       = Carbon::createFromFormat('Y-m-d H:i:s', $fechaFin, $pais)->tz('UTC');
    //                 }

    //                 $consumos = PropiedadServicio::where('propiedad_id', $propiedad_id)
    //                 ->where('created_at','>=' , $fecha_inicio)
    //                 ->where('created_at', '<' , $fecha_fin)
    //                 ->with('servicio')
    //                 ->with('TipoComprobante')
    //                 ->with('tipoMoneda')
    //                 ->get();

    //                 $i = 1;
    //                 foreach ($moneda_propiedad as $moneda) {
    //                     $total = 0;
    //                     foreach ($consumos as $consumo) { 
    //                         if ($moneda->id == $egreso->tipo_moneda_id) {
    //                             $total += $egreso->monto;
    //                         }
    //                     }
    //                     $csm['moneda-'.$i]      = $moneda->nombre;
    //                     $csm['monto-'.$i]       = $total;
    //                     $csm['mes']             = $mes_año;
    //                     $csm['fecha_inicio']    = $m[$mes_año]['inicio'];
    //                     $csm['fecha_fin']       = $m[$mes_año]['fin'];
    //                     $i++;
    //                 }
    //                 $consumos = $csm;
    //             }
    //         }
    //         array_push($ingresos_mes, $consumos);
    //     }

    //     $grafico =[ 'grafico_1' => $ingresos_mes];
    //     return $grafico;
    // }




    /**
     * Reporte egresos de propiedad y caja por fechas
     *
     * @author ALLEN
     *
     * @param  Request          $request (propiedad_id, $fecha_inicio, $fecha_fin)
     * @return Response::json
     */
    public function reporteEgresos(Request $request)
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

        $moneda_propiedad    = $propiedad->tipoMonedas;
        $prop_egresos        = Egreso::where('propiedad_id', $propiedad_id)->get();

        $caja_egresos = EgresoCaja::whereHas('caja', function($query) use($propiedad_id){
                $query->where('propiedad_id', $propiedad_id);
        })->where('created_at', '>=' , $fecha_inicio)->where('created_at', '<' , $fecha_fin)->get();

        $propiedad_egresos = EgresoPropiedad::where('propiedad_id', $propiedad_id)->where('created_at', '>=' , $fecha_inicio)->where('created_at', '<' , $fecha_fin)->get();

        $egresos_caja_propiedad = [];
        $tipo_egresos           = [];
        $egresos_caja           = [];
        $egresos_propiedad      = [];
        foreach ($prop_egresos as $egreso) {
            $egresos_moneda = [];
            foreach ($moneda_propiedad as $moneda) {
                $suma_egreso            = 0;
                foreach ($caja_egresos as $egreso_caja) {
                    if ($moneda->id == $egreso_caja->tipo_moneda_id) {
                        if ($egreso->id == $egreso_caja->egreso_id) {
                            $suma_egreso += $egreso_caja->monto;
                        }
                    }
                }
                foreach ($propiedad_egresos as $egreso_propiedad) {
                    if ($moneda->id == $egreso_propiedad->tipo_moneda_id) {
                        if ($egreso->id == $egreso_propiedad->egreso_id) {
                            $suma_egreso += $egreso_propiedad->monto;
                        }
                    }
                }

                $egresos['monto']                   = $suma_egreso;
                $egresos['tipo_moneda_id']          = $moneda->id;
                $egresos['nombre_moneda']           = $moneda->nombre;
                $egresos['cantidad_decimales']      = $moneda->cantidad_decimales;  
                array_push($egresos_moneda, $egresos);
            }
            $auxEgresos['id']           = $egreso->id;
            $auxEgresos['nombre']       = $egreso->nombre;
            $auxEgresos['egresos']      = $egresos_moneda;
            array_push($egresos_caja_propiedad, $auxEgresos);
        }

        foreach ($moneda_propiedad as $moneda) {
            $suma_egresos_caja      = 0;
            $suma_egresos_propiedad = 0;
            foreach ($caja_egresos as $egreso_caja) {
                if ($moneda->id == $egreso_caja->tipo_moneda_id) {
                    $suma_egresos_caja += $egreso_caja->monto;
                }
            }
            foreach ($propiedad_egresos as $egreso_propiedad) {
                if ($moneda->id == $egreso_propiedad->tipo_moneda_id) {
                    $suma_egresos_propiedad += $egreso_propiedad->monto;
                }
            }
            $aux_egresos_caja['monto']                   = $suma_egresos_caja;
            $aux_egresos_caja['tipo_moneda_id']          = $moneda->id;
            $aux_egresos_caja['nombre_moneda']           = $moneda->nombre;
            $aux_egresos_caja['cantidad_decimales']      = $moneda->cantidad_decimales;  
            array_push($egresos_caja, $aux_egresos_caja);

            $aux_egresos_propiedad['monto']                   = $suma_egresos_propiedad;
            $aux_egresos_propiedad['tipo_moneda_id']          = $moneda->id;
            $aux_egresos_propiedad['nombre_moneda']           = $moneda->nombre;
            $aux_egresos_propiedad['cantidad_decimales']      = $moneda->cantidad_decimales; 
            array_push($egresos_propiedad, $aux_egresos_propiedad);
        }

        $total_egresos = [];
        foreach ($moneda_propiedad as $moneda) {
            $aux_total = 0;
            foreach ($egresos_caja as $egreso) {
                if ($egreso['tipo_moneda_id'] == $moneda->id) {
                    $aux_total += $egreso['monto'];
                }
            }
            foreach ($egresos_propiedad as $egreso) {
                if ($egreso['tipo_moneda_id'] == $moneda->id) {
                    $aux_total += $egreso['monto'];
                }
            }
            $total['monto']              = $aux_total;
            $total['tipo_moneda_id']     = $moneda->id;
            $total['nombre_moneda']      = $moneda->nombre;
            $total['cantidad_decimales'] = $moneda->cantidad_decimales;
            array_push($total_egresos, $total);
        }

        $data['egresos_total_caja']        = $egresos_caja;
        $data['egresos_total_propiedad']   = $egresos_propiedad;
        $data['total_egresos']             = $total_egresos;
        $data['egresos']                   = $egresos_caja_propiedad;
        
        return $data;

    }

    public function reportes(Request $request)
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
        $ingresos_habitacion  = [];
        $ingresos_consumos    = [];

        foreach ($propiedad->tipoMonedas as $moneda) {
            $tipo_moneda_id    = $moneda->pivot->tipo_moneda_id;
            $pagos_tipo_moneda = $pagos->where('tipo_moneda_id', $tipo_moneda_id);
            $suma_pagos              = 0;
            $ingresos_por_habitacion = 0;
            $ingresos_por_consumos   = 0;

            foreach ($pagos_tipo_moneda as $pago) {
                if ($pago->estado == 1) {
                    $suma_pagos += $pago->monto_equivalente;
                    if ($pago->tipo == 'Pago habitacion') {
                        $ingresos_por_habitacion += $pago->monto_equivalente;
                    } elseif ($pago->tipo == 'Pago consumos'){
                    $ingresos_por_consumos += $pago->monto_equivalente;
                    } elseif ($pago->tipo == 'Confirmacion de reserva') {
                        $ingresos_por_habitacion += $pago->monto_equivalente;

                    }
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
            foreach ($reservas as $reserva) {
                if ($reserva->checkin <= $auxFecha_inicio && $reserva->checkout > $auxFecha_inicio) {
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


    /**
     * Reporte de pagos de propiedad
     *
     * @author ALLEN
     *
     * @param  Request          $request (propiedad_id, $fecha_inicio, $fecha_fin)
     * @return Response::json
     */
    public function pagos(Request $request, Pago $pago)
    {
        if ($request->has('propiedad_id')) {
            $propiedad_id = $request->input('propiedad_id');
            $propiedad    = Propiedad::where('id', $propiedad_id)->with('tipoMonedas')->first();
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

        $pago = $pago->newQuery();

        $pago->whereHas('reserva.habitacion', function($query) use($propiedad_id, $fecha_inicio, $fecha_fin){
            $query->where('propiedad_id', $propiedad_id);})
        ->where('pagos.created_at','>=' , $fecha_inicio)
        ->where('pagos.created_at', '<' , $fecha_fin);

        $indicador = $request->get('indicador');

        if ($indicador == 1) {
            
            if ($request->has('tipo_comprobante_id')) {
                $tipos_comprobante = $request->get('tipo_comprobante_id');

                $pago->whereHas('reserva.habitacion', function($query) use($propiedad_id, $fecha_inicio, $fecha_fin){
                $query->where('propiedad_id', $propiedad_id);})
                ->where(function ($query) use ($tipos_comprobante) {
                    $query->where(function ($query) use ($tipos_comprobante) {
                        $query->whereIn('tipo_comprobante_id',  $tipos_comprobante);
                        $query->orWhere('tipo_comprobante_id', '=', null);
                    });              
                });
            }
            
        } else {

            if ($request->has('tipo_comprobante_id')) {
                $tipos_comprobante = $request->get('tipo_comprobante_id');

                $pago->whereHas('reserva.habitacion', function($query) use($propiedad_id, $fecha_inicio, $fecha_fin){
                $query->where('propiedad_id', $propiedad_id);})
                ->where(function ($query) use ($tipos_comprobante) {
                    $query->where(function ($query) use ($tipos_comprobante) {
                        $query->whereIn('tipo_comprobante_id',  $tipos_comprobante);
                    });              
                });
            }
        }

        if ($request->has('metodo_pago_id')) {
            $metodos_pago = $request->get('metodo_pago_id');

            $pago->whereHas('reserva.habitacion', function($query) use($propiedad_id){
                $query->where('propiedad_id', $propiedad_id);
            })->where(function ($query) use ($metodos_pago) {
                $query->where(function ($query) use ($metodos_pago) {
                $query->whereIn('metodo_pago_id', $metodos_pago);
            });
            });
        }

        $pagos = $pago->select('pagos.id', 'reservas.id as reserva_id' ,'pagos.created_at','numero_reserva','numero_operacion', 'tipo' ,'monto_equivalente','numero_cheque', 'monto_equivalente','metodo_pago.nombre as nombre_metodo_pago' , 'metodo_pago_id','tipo_moneda.nombre as nombre_tipo_moneda','pagos.tipo_moneda_id', 'cantidad_decimales', 'tipo_comprobante_id')
        ->with(['tipoComprobante' => function ($q){
            $q->select('id', 'nombre');}])
        ->where('pagos.created_at','>=' , $fecha_inicio)->where('pagos.created_at', '<' , $fecha_fin)
        ->join('reservas' , 'reservas.id', '=' , 'pagos.reserva_id')
        ->join('tipo_moneda', 'tipo_moneda.id', '=' , 'pagos.tipo_moneda_id')
        ->join('metodo_pago', 'metodo_pago.id', '=' , 'pagos.metodo_pago_id')
        ->get();

        $cantidad_noches    = ($fecha_inicio->diffInDays($fecha_fin)) ;
        $fechas             = [];
        $monto              = 0;
        $montos             = [];
        foreach ($propiedad->tipoMonedas as $moneda) {
            $m['id']     = $moneda->id;
            $m['nombre'] = $moneda->nombre;
            $m['cantidad_decimales'] = $moneda->cantidad_decimales;
            $m['suma'] = 0;

            array_push($montos, $m);
        }

        $auxFecha  = new Carbon($request->input('fecha_inicio'));
        for( $i = 0 ; $i <= $cantidad_noches; $i++){

            $fecha      = $auxFecha->format('Y-m-d');
            $fechas[$i] = ['fecha' => $fecha, 'moneda' => $montos, 'pagos_dia' => []];

            $auxFecha->addDay();
        }

        $ini  = new Carbon($request->input('fecha_inicio'));
        $inc  = $ini->startOfDay();


        foreach ($pagos as $pago) {
            $created_at  = new Carbon($pago->created_at);
            $crat        = $created_at->startOfDay();
            $dif         = $inc->diffInDays($crat); 
            $largo       = sizeof($fechas[$dif]['moneda']);
            array_push($fechas[$dif]['pagos_dia'], $pago);

            for( $i = 0 ; $i < $largo ; $i++){
                if ($fechas[$dif]['moneda'][$i]['id'] == $pago->tipo_moneda_id ){
                    $fechas[$dif]['moneda'][$i]['suma'] += $pago->monto_equivalente;
                }
            }
        }


        $fechas_montos = [];
        foreach ($fechas as $fecha) {
            $largo = count($fecha['moneda']);
            for( $i = 0 ; $i < $largo ; $i++){
                if ($fecha['moneda'][$i]['suma'] != 0 ){
                    if (!in_array($fecha, $fechas_montos)) {
                        array_push($fechas_montos, $fecha);
                    }
                }
            }
        }
        
        return $fechas_montos;

    }

    /**
     * Obtiene pagos de un dia
     *
     * @author ALLEN
     *
     * @param  Request          $request (propiedad_id, fecha_inicio, fecha_fin)
     * @return Response::json
     */
    public function getPagos(Request $request)
    {
        if ($request->has('propiedad_id')) {
            $propiedad_id = $request->input('propiedad_id');
            $propiedad    = Propiedad::where('id', $propiedad_id)->with('tipoMonedas')->first();
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


        $pagos = Pago::select('pagos.id', 'reservas.id as reserva_id' ,'pagos.created_at','numero_reserva','numero_operacion', 'tipo' ,'monto_equivalente','numero_cheque', 'monto_equivalente','metodo_pago.nombre as nombre_metodo_pago' , 'metodo_pago_id','tipo_moneda.nombre as nombre_tipo_moneda','pagos.tipo_moneda_id', 'cantidad_decimales', 'tipo_comprobante_id')
        ->whereHas('reserva.habitacion', function($query) use($propiedad_id){
            $query->where('propiedad_id', $propiedad_id);})
        ->with(['tipoComprobante' => function ($q){
            $q->select('id', 'nombre');}])
        ->where('pagos.created_at','>=' , $fecha_inicio)->where('pagos.created_at', '<' , $fecha_fin)
        ->join('reservas' , 'reservas.id', '=' , 'pagos.reserva_id')
        ->join('tipo_moneda', 'tipo_moneda.id', '=' , 'pagos.tipo_moneda_id')
        ->join('metodo_pago', 'metodo_pago.id', '=' , 'pagos.metodo_pago_id')
        ->get();


        return $pagos;

    }

    public function crearPoliticas(Request $request)
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

        if ($request->has('politicas')) {
            $politicas = $request->input('politicas');
        } else {
            $retorno = array(
                'msj'    => "No se envia propiedad_id",
                'errors' => true);
            return Response::json($retorno, 400);
        }

        foreach ($politicas as $pt) {
            $politica                  = new Politica();
            $politica->descripcion     = $pt['descripcion'];
            $politica->propiedad_id    = $propiedad_id;
            $politica->save();
        }

        $retorno = array(
            'msj'   => "Políticas ingresadas satisfactoriamente",
            'erros' => false,);
        return Response::json($retorno, 201);

    }

    public function getPoliticasPropiedad(Request $request)
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

        $politicas = Politica::where('propiedad_id', $propiedad_id)->get();
        return $politicas;

    }

    public function editarPolitica(Request $request,$id)
    {
        $rules = array(
            'descripcion'     => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $data = [
                'errors' => true,
                'msg'    => $validator->messages(),];
            return Response::json($data, 400);

        } else {

            $politica = Politica::findOrFail($id);
            $politica->update($request->all());
            $politica->touch();

            $data = [
                'errors' => false,
                'msj'    => 'Politica actualizada satisfactoriamente',];
            return Response::json($data, 201);
        }

    }

    public function eliminarPolitica($id)
    {
        $politica = Politica::findOrFail($id);
        $politica->delete();

        $retorno = array(
            'errors' => false,
            'msj'    => 'Politica eliminado satisfactoriamente',);
        return Response::json($retorno, 202);

    }

    public function ingresoServicio(Request $request)
    {
        if ($request->has('venta_servicio') && $request->has('propiedad_id') && $request->has('metodo_pago_id')) {
            $propiedad           = Propiedad::where('id', $request->input('propiedad_id'))->first();
            $metodo_pago_id      = $request->input('metodo_pago_id');
            $numero_operacion    = $request->input('numero_operacion');
            $tipo_comprobante_id = $request->input('tipo_comprobante_id');
            $numero_cheque       = $request->input('numero_cheque');
            $tipo_moneda_id      = $request->input('tipo_moneda_id');

            $caja_abierta  = Caja::where('propiedad_id', $propiedad->id)->where('estado_caja_id', 1)->first();

            if (!is_null($caja_abierta)) {
                if (!is_null($propiedad)) {
                    $servicios = $request->input('venta_servicio');
                    foreach ($servicios as $servicio) {
                        $servicio_id         = $servicio['servicio_id'];
                        $cantidad            = $servicio['cantidad'];
                        $precio_total        = $servicio['precio_total'];
                        $serv                = Servicio::where('id', $servicio_id)->where('propiedad_id', $request->input('propiedad_id'))->first();
                        $cantidad_disponible = $serv->cantidad_disponible;

                        if (!is_null($serv)) {
                            if ($serv->categoria_id == 2) {
                                if ($cantidad >= 1) {
                                    if ($serv->cantidad_disponible > 0) {
                                        if ($cantidad <= $serv->cantidad_disponible) {
                                            $servicio_id         = $serv->id;
                                            $servicio_nombre     = $serv->nombre;
                                            $cantidad_disponible = $cantidad_disponible - $cantidad;
                                            $serv->update(array('cantidad_disponible' => $cantidad_disponible));
                                            $propiedad->vendeServicios()->attach($servicio_id, ['metodo_pago_id' => $metodo_pago_id, 'cantidad' => $cantidad, 'precio_total' => $precio_total, 'numero_operacion' => $numero_operacion, 'tipo_comprobante_id' => $tipo_comprobante_id, 'numero_cheque' => $numero_cheque, 'tipo_moneda_id' => $tipo_moneda_id, 'caja_id' => $caja_abierta->id]);

                                        } else {
                                            $data = array(
                                                'msj'    => " La cantidad ingresada es mayor al stock del producto",
                                                'errors' => true,);
                                            return Response::json($data, 400);
                                        }

                                    } else {
                                        $data = array(
                                            'msj'    => " El servicio no tiene stock",
                                            'errors' => true,);
                                        return Response::json($data, 400);
                                    }

                                } else {
                                    $data = array(
                                        'msj'    => " La cantidad ingresada no corresponde",
                                        'errors' => true,);
                                    return Response::json($data, 400);
                                }

                            } elseif ($serv->categoria_id == 1) {
                                $propiedad->vendeServicios()->attach($servicio_id, ['metodo_pago_id' => $metodo_pago_id, 'cantidad' => $cantidad, 'precio_total' => $precio_total, 'numero_operacion' => $numero_operacion, 'tipo_comprobante_id' => $tipo_comprobante_id, 'numero_cheque' => $numero_cheque, 'tipo_moneda_id' => $tipo_moneda_id, 'caja_id' => $caja_abierta->id]);
                            }

                        } else {
                            $retorno = array(
                                'msj'    => "El servicio no pertenece a la propiedad",
                                'errors' => true,);
                            return Response::json($retorno, 400);
                        }
                    }
                    $retorno = array(
                        'msj'   => "Servicios ingresados correctamente",
                        'erros' => false,);
                    return Response::json($retorno, 201);

                } else {
                    $data = array(
                        'msj'    => "Propiedad no encontrada",
                        'errors' => true,);
                    return Response::json($data, 404);
                }
                # code...
            } else {
                $retorno = array(
                    'msj'    => "No hay caja abierta",
                    'errors' => true);
                return Response::json($retorno, 400);
            }


        } else {
            $retorno = array(
                'msj'    => "La solicitud esta incompleta",
                'errors' => true,);
            return Response::json($retorno, 400);
        }

    }

    public function getConsumosParticulares(Request $request)
    {
        if ($request->has('propiedad_id')) {
            $propiedad_id = $request->input('propiedad_id');
            $propiedad    = Propiedad::where('id', $propiedad_id)->with('tipoMonedas')->first();
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

        $consumos = PropiedadServicio::where('propiedad_id', $propiedad_id)
        ->where('created_at','>=' , $fecha_inicio)
        ->where('created_at', '<' , $fecha_fin)
        ->with('servicio')
        ->with('TipoComprobante')
        ->with('metodoPago')
        ->with('tipoMoneda')
        ->get();

        $num_op = [];
        $num_operacion = $consumos->lists('numero_operacion');
        foreach ($num_operacion as $num) {
            if (count($num_op) == 0) {
                array_push($num_op, $num);
            } else {
                if (!in_array($num, $num_op)) {
                    array_push($num_op, $num);
                }
            }
        }

        $monedas = [];
        foreach ($propiedad->tipoMonedas as $moneda) {
            $total = 0;
            foreach ($consumos as $consumo) {
                if ($moneda->id == $consumo->tipo_moneda_id) {
                    $total += $consumo->precio_total;
                }
            }
            $m['id']     = $moneda->id;
            $m['nombre'] = $moneda->nombre;
            $m['cantidad_decimales'] = $moneda->cantidad_decimales;
            $m['total'] = $total;
            array_push($monedas, $m);
        }

        $nums = [];
        $cantidad = count($consumos);
        foreach ($num_op as $num) {
            $cons = [];
            $total_precio = 0;
            foreach ($consumos as $consumo) {
                if ($num == $consumo->numero_operacion) {
                    $total_precio += $consumo->precio_total;
                    array_push($cons, $consumo);
                        $tipo_moneda_id     = $cons[0]->tipoMoneda->id;
                        $cantidad_decimales = $cons[0]->tipoMoneda->cantidad_decimales;
                        $nombre             = $cons[0]->tipoMoneda->nombre;
                        $tipo_comprobante   = $cons[0]->tipoComprobante->nombre;
                        $metodo_pago        = $cons[0]->metodoPago->nombre;
                    
                }
            }
            $csm['numero_operacion']    = $num;
            $csm['total']               = $total_precio;
            $csm['tipo_moneda_id']      = $tipo_moneda_id;
            $csm['cantidad_decimales']  = $cantidad_decimales;
            $csm['nombre']              = $nombre;
            $csm['tipo_comprobante']    = $tipo_comprobante;
            $csm['metodo_pago']         = $metodo_pago;
            $csm['consumos']            = $cons;
            array_push($nums, $csm);
        }
        $data['monedas']  = $monedas;
        $data['consumos'] = $nums;

        return $data;
    }

    /**
     * editar consumos de particulares
     *
     * @author ALLEN
     *
     * @param  Request          $request ()
     * @return Response::json
     */
    public function editarConsumoParticulares(Request $request)
    {
        if ($request->has('servicio_id')) {
            $servicio_id = $request->input('servicio_id');
            $servicio    = PropiedadServicio::where('id', $servicio_id)->first();
            if (is_null($servicio)) {
                $retorno = array(
                    'msj'    => "Servicio no encontrado",
                    'errors' => true);
                return Response::json($retorno, 404);
            }
        } else {
            $retorno = array(
                'msj'    => "No se envia servicio_id",
                'errors' => true);
            return Response::json($retorno, 400);
        }

        if ($request->has('cantidad') && $request->has('precio_total') && $request->has('numero_operacion') && $request->has('metodo_pago_id') && $request->has('tipo_comprobante_id') && $request->has('numero_cheque')) {
            $cantidad = $request->cantidad;
            $precio_total = $request->precio_total;
            $numero_operacion = $request->numero_operacion;
            $metodo_pago_id = $request->metodo_pago_id;
            $tipo_comprobante_id = $request->tipo_comprobante_id;
            $numero_cheque = $request->numero_cheque;
        } else {
            $retorno = array(
                'msj'    => "La solicitud esta incompleta",
                'errors' => true,);
            return Response::json($retorno, 400);
        }

        $servicio->update(array('cantidad' => $cantidad, 'precio_total' => $precio_total, 'numero_operacion' => $numero_operacion, 'metodo_pago_id' => $metodo_pago_id, 'tipo_comprobante_id' => $tipo_comprobante_id, 'numero_cheque' => $numero_cheque));

        $data = array(
            'errors' => false,
            'msj'    => 'Servicio actualizado satisfactoriamente',);
        return Response::json($data, 400);

    }

    /**
     * Eliminar consumos de particulares
     *
     * @author ALLEN
     *
     * @param  Request          $request ()
     * @return Response::json
     */

    public function eliminarConsumosParticulares(Request $request)
    {
        if ($request->has('servicios')) {
            $servicios = $request->servicios;

            foreach ($servicios as $servicio) {
                $servicio  = PropiedadServicio::findOrFail($servicio);
                $servicio->delete();
            }

            $retorno = array(
                'errors' => false,
                'msg'    => 'Servicios eliminado satisfactoriamente',);
            return Response::json($retorno, 202);

        } else {
            $retorno = array(
                'msj'    => "La solicitud esta incompleta",
                'errors' => true,);
            return Response::json($retorno, 400);
        }
    }

    public function index(Request $request)
    {
        if ($request->has('id')) {

            $propiedad = Propiedad::where('id', $request->input('id'))->with('tipoPropiedad','pais','region','zonaHoraria' ,'tipoMonedas','coloresMotor' ,'tipoCobro', 'politicas', 'cuentasBancaria.tipoCuenta', 'tipoDepositoPropiedad.tipoDeposito')->get();
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
                    'erros' => false,);
                return Response::json($retorno, 201);
            } else {
                $retorno = array(
                    'msj'    => "Propiedad no encontrada",
                    'errors' => true,);
                return Response::json($retorno, 404);
            }
        } else {
            $retorno = array(
                'msj'    => "La solicitud esta incompleta",
                'errors' => true,);
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

    public function crearCuentaBancaria(Request $request)
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

        if ($request->has('nombre_banco') && $request->has('numero_cuenta') && $request->has('titular') && $request->has('rut') && $request->has('email') && $request->has('tipo_cuenta_id')) {
            $nombre_banco   = $request->input('nombre_banco');
            $numero_cuenta  = $request->input('numero_cuenta');
            $titular        = $request->input('titular');
            $rut            = $request->input('rut');
            $email          = $request->input('email');
            $tipo_cuenta_id = $request->input('tipo_cuenta_id');

            $cuenta                     = new CuentaBancaria();
            $cuenta->nombre_banco       = $nombre_banco;
            $cuenta->numero_cuenta      = $numero_cuenta;
            $cuenta->titular            = $titular;
            $cuenta->rut                = $rut;
            $cuenta->email              = $email;
            $cuenta->tipo_cuenta_id     = $tipo_cuenta_id;
            $cuenta->propiedad_id       = $propiedad_id;
            $cuenta->save();    

            $retorno = array(
                'msj'   => "Cuenta creada satisfactoriamente",
                'erros' => false,);
            return Response::json($retorno, 201);

        } else {
            $retorno = array(
                'msj'    => "La solicitud esta incompleta",
                'errors' => true,);
            return Response::json($retorno, 400);
        }

    }

    public function editarCuentaBancaria(Request $request,$id)
    {
        $rules = array(
            'nombre_banco'    => '', 
            'numero_cuenta'   => '',
            'titular'         => '',
            'rut'             => '',
            'email'           => '',
            'tipo_cuenta_id'  => 'numeric',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $data = [
                'errors' => true,
                'msg'    => $validator->messages(),];
            return Response::json($data, 400);

        } else {

            $cuenta = CuentaBancaria::findOrFail($id);
            $cuenta->update($request->all());
            $cuenta->touch();

            $data = [
                'errors' => false,
                'msg'    => 'Cuenta actualizada satisfactoriamente',];
            return Response::json($data, 201);
        }

    }

    public function crearTipoDepositoPropiedad(Request $request)
    {
        $rules = array(
            'valor'            => 'numeric', 
            'propiedad_id'     => 'required|numeric',
            'tipo_deposito_id' => 'required|numeric',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $data = [
                'errors' => true,
                'msj'    => $validator->messages(),];
            return Response::json($data, 400);

        } else {

            $propiedad_id = $request->input('propiedad_id');
            $propiedad    = Propiedad::where('id', $propiedad_id)->first();
            if (!is_null($propiedad)) {
                $tipo_deposito                   = new PropiedadTipoDeposito();
                $tipo_deposito->valor            = $request->input('valor');
                $tipo_deposito->propiedad_id     = $request->input('propiedad_id');
                $tipo_deposito->tipo_deposito_id = $request->input('tipo_deposito_id');
                $tipo_deposito->save(); 
            } else {
                $retorno = array(
                    'msj'    => "Propiedad no encontrada",
                    'errors' => true);
                return Response::json($retorno, 404);
            }

            $data = [
                'errors' => false,
                'msj'    => 'Tipo deposito creado satisfactoriamente',];
            return Response::json($data, 201);
        }

    }

    public function editarTipoDepositoPropiedad(Request $request, $id)
    {
        $rules = array(
            'valor'              => 'numeric', 
            'tipo_deposito_id'   => 'numeric',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $data = [
                'errors' => true,
                'msg'    => $validator->messages(),];
            return Response::json($data, 400);

        } else {

            $tipo_deposito = PropiedadTipoDeposito::findOrFail($id);
            $tipo_deposito->update($request->all());
            $tipo_deposito->touch();

            $data = [
                'errors' => false,
                'msg'    => 'Tipo deposito actualizado satisfactoriamente',];
            return Response::json($data, 201);
        }

    }

    public function eliminarTipoDepositoPropiedad($id)
    {
        $tipo_deposito = PropiedadTipoDeposito::findOrFail($id);
        $tipo_deposito->delete();

        $data = [
            'errors' => false,
            'msg'    => 'Tipo deposito eliminado satisfactoriamente',];
        return Response::json($data, 202);

    }

    public function eliminarCuentaBancaria($id)
    {
        $cuenta = CuentaBancaria::findOrFail($id);
        $cuenta->delete();

        $data = [
            'errors' => false,
            'msg'    => 'Cuenta eliminada satisfactoriamente',];
        return Response::json($data, 202);

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

    public function getPaises()
    {
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

    public function getTipoCuenta()
    {
        $tipoCuenta = TipoCuenta::all();
        return $tipoCuenta;

    }

    public function getTipoDeposito()
    {
        $tipoDeposito = TipoDeposito::all();
        return $tipoDeposito;

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

    public function CrearCodigo()
    {
        $propiedades = Propiedad::all();
        foreach ($propiedades as $propiedad) {
            $propiedad = Propiedad::where('id', $propiedad->id)->first();
            $propiedad->update(array('codigo' => str_random(50)));

        }

        return "creados";

    }



}