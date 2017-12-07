<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use PDF;
use App\Reserva;
use App\Propiedad;
use App\Cliente;
use App\Huesped;
use App\Habitacion;
use App\Pago;
use App\ZonaHoraria;
use App\Region;
use App\TipoMoneda;
use App\MetodoPago;
use App\TipoHabitacion;
use App\TipoCliente;
use App\Servicio;
use App\TipoFuente;
use App\Caja;
use \Carbon\Carbon;
use Response;

class PDFController extends Controller
{

    public function reservas(Request $request, Reserva $reserva)
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

        $reserva = $reserva->newQuery();

        if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
            $inicio = new Carbon($request->input('fecha_inicio'));
            $fin    = new Carbon($request->input('fecha_fin'));

            $fecha_inicio = $inicio->startOfDay()->format('Y-m-d');
            $fecha_fin    = $fin->startOfDay()->format('Y-m-d');


        $reserva->whereHas('habitacion', function($query) use($propiedad_id){
                $query->where('propiedad_id', $propiedad_id);
            })->where(function ($query) use ($fecha_inicio, $fecha_fin) {
            $query->where(function ($query) use ($fecha_inicio, $fecha_fin) {
                $query->where('checkin', '>=', $fecha_inicio);
                $query->where('checkin', '<',  $fecha_fin);
                        });
            $query->orWhere(function($query) use ($fecha_inicio,$fecha_fin){
                $query->where('checkin', '<=', $fecha_inicio);
                $query->where('checkout', '>',  $fecha_inicio);
        });                
        });

        }
        
        if ($request->has('nombre')) {
            $nombre = $request->input('nombre');

            $reserva->whereHas('habitacion', function($query) use($propiedad_id){
                $query->where('propiedad_id', $propiedad_id);
            })->where(function ($query) use ($nombre) {
                $query->where(function ($query) use ($nombre) {
                    $query->whereHas('cliente', function ($query) use ($nombre) {
                    $query->where('nombre', $nombre);
                    });
            });
            });

        }

        if ($request->has('apellido')) {
            $apellido = $request->input('apellido');

            $reserva->whereHas('habitacion', function($query) use($propiedad_id){
                $query->where('propiedad_id', $propiedad_id);
            })->where(function ($query) use ($apellido) {
                $query->where(function ($query) use ($apellido) {
                    $query->whereHas('cliente', function ($query) use ($apellido) {
                    $query->where('apellido', $apellido);
                    });
                
            });
            });

        }

        if ($request->has('rut')) {
            $rut = $request->input('rut');

            $reserva->whereHas('habitacion', function($query) use($propiedad_id){
                $query->where('propiedad_id', $propiedad_id);
            })->where(function ($query) use ($rut) {
                $query->where(function ($query) use ($rut) {
                    $query->whereHas('cliente', function ($query) use ($rut) {
                    $query->where('rut', $rut);
                    });
            });
            });

        }

        if ($request->has('numero_reserva')) {
            $numero_reserva = $request->input('numero_reserva');

            $reserva->whereHas('habitacion', function($query) use($propiedad_id){
                $query->where('propiedad_id', $propiedad_id);
            })->where(function ($query) use ($numero_reserva) {
                $query->where(function ($query) use ($numero_reserva) {
                    $query->where('numero_reserva', $numero_reserva);
            });
            });
        }

        if ($request->has('estado_reserva_id')) {
            $estado_reserva = $request->get('estado_reserva_id');

            $reserva->whereHas('habitacion', function($query) use($propiedad_id){
                $query->where('propiedad_id', $propiedad_id);
            })->where(function ($query) use ($estado_reserva) {
                $query->where(function ($query) use ($estado_reserva) {
                    $query->whereIn('estado_reserva_id', $estado_reserva);
            });
            });

        }

        if ($request->has('tipo_fuente_id')) {
            $tipo_fuente = $request->get('tipo_fuente_id');

            $reserva->whereHas('habitacion', function($query) use($propiedad_id){
                $query->where('propiedad_id', $propiedad_id);
            })->where(function ($query) use ($tipo_fuente) {
                $query->where(function ($query) use ($tipo_fuente) {
                    $query->whereIn('tipo_fuente_id', $tipo_fuente);
            });
            });

        }

        $reservas = $reserva->select('reservas.id', 'numero_reserva' ,'checkin', 'habitacion_id', 'estado_reserva_id' ,'checkout', 'monto_total','estado_reserva.nombre as estado' ,'cliente_id', 'clientes.nombre as nombre_cliente', 'clientes.apellido as apellido_cliente', 'noches', 'tipo_moneda.nombre as nombre_moneda')
        ->whereHas('habitacion', function($query) use($propiedad_id){
        $query->where('propiedad_id', $propiedad_id);})
        ->with(['huespedes' => function ($q){
        $q->select('huespedes.id', 'nombre', 'apellido');}])
        ->with('habitacion.tipoHabitacion')
        ->join('clientes', 'clientes.id','=','cliente_id')
        ->join('tipo_moneda', 'tipo_moneda.id', '=', 'tipo_moneda_id')
        ->join('estado_reserva', 'estado_reserva.id', '=', 'estado_reserva_id')
        ->get();

        $pdf = PDF::loadView('pdf.reservas', ['propiedad' => [$propiedad], 'reservas' => $reservas]);

        return $pdf->download('archivo.pdf');

    }

    public function caja(Request $request)
    {
        if ($request->has('caja_id')) {
            $caja_id = $request->input('caja_id');
            $caja    = Caja::where('id', $caja_id)->first();
            if (is_null($caja)) {
                $retorno = array(
                    'msj'    => "Caja no encontrada",
                    'errors' => true);
                return Response::json($retorno, 404);
            }
        } else {
            $retorno = array(
                'msj'    => "No se envia caja_id",
                'errors' => true);
            return Response::json($retorno, 400);
        }

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

        $caja  = Caja::where('id', $caja_id)->with('montos.tipoMonto', 'montos.tipoMoneda')->with('user')->with('estadoCaja')->with('pagos.tipoComprobante','pagos.metodoPago', 'pagos.tipoMoneda', 'pagos.reserva')->with('egresosCaja.tipoMoneda', 'egresosCaja.egreso')->first();

        if (!is_null($caja)) {
            $monedas = [];
            foreach ($propiedad->tipoMonedas as $tipo_moneda) {
                $ingreso = 0;
                $egreso  = 0;
                foreach ($caja->pagos as $pago) {
                    if ($tipo_moneda->id == $pago->tipo_moneda_id) {
                        if ($pago->metodo_pago_id == 1 || $pago->metodo_pago_id == 4) {
                            $ingreso += $pago->monto_equivalente;
                        }
                    }
                }
                foreach ($caja->egresosCaja as $egreso_caja) {
                    if ($tipo_moneda->id == $egreso_caja->tipo_moneda_id) {
                        $egreso += $egreso_caja->monto;
                    }
                }

                $moneda['nombre']               = $tipo_moneda->nombre;
                $moneda['cantidad_decimales']   = $tipo_moneda->cantidad_decimales;
                $moneda['ingreso']              = $ingreso;
                $moneda['egreso']               = $egreso;
                $moneda['saldo']                = ($ingreso-$egreso);
                array_push($monedas, $moneda);
            }

            $metodo_pagos              = MetodoPago::all();
            $ingresos_metodo_pago      = [];
            foreach ($metodo_pagos as $metodo) {
                $ingresos_moneda = [];
                foreach ($propiedad->tipoMonedas as $moneda) {
                    $suma_ingreso   = 0;
                    foreach ($caja->pagos  as $pago) {
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

            $data['caja']         = $caja;
            $data['monedas']      = $monedas;
            $data['metodos_pago'] = $ingresos_metodo_pago;

            //return ['propiedad' => [$propiedad], 'detalle_caja' => [$caja], 'monedas' => $monedas, 'metodos_pago' => $ingresos_metodo_pago];

            $pdf = PDF::loadView('pdf.caja', ['propiedad' => [$propiedad], 'detalle_caja' => [$caja], 'monedas' => $monedas, 'metodos_pago' => $ingresos_metodo_pago]);

            return $pdf->download('archivo.pdf');

        }

    }



    public function entradas(Request $request)
    {
        if ($request->has('propiedad_id')) {
            $id = $request->input('propiedad_id');
            $propiedad    = Propiedad::where('id', $id)->first();
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

        $enUTC           = Carbon::now();
        $zona_horaria    = ZonaHoraria::where('id', $propiedad->zona_horaria_id)->first();
        $pais            = $zona_horaria->nombre;
        $fecha_pais      = $enUTC->tz($pais);
        $fecha           = $fecha_pais->format('Y-m-d');
        $dia             = $fecha_pais->format('d-m-Y');


        $reservas_hoy = Reserva::whereHas('habitacion', function($query) use($id){
                    $query->where('propiedad_id', $id);
        })->where('checkin', $fecha)->with('habitacion.tipoHabitacion')->with('tipoMoneda')->with('huespedes')->with('cliente.pais', 'cliente.region')->with('estadoReserva')->whereIn('estado_reserva_id', [1,2,3])->get();

        $pdf = PDF::loadView('pdf.entradas', ['fecha' => $dia, 'propiedad' => [$propiedad], 'reservas' => $reservas_hoy]);

        return $pdf->download('archivo.pdf');

    }

    public function salidas(Request $request)
    {
        if ($request->has('propiedad_id')) {
            $id = $request->input('propiedad_id');
            $propiedad    = Propiedad::where('id', $id)->first();
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

        $enUTC           = Carbon::now();
        $zona_horaria    = ZonaHoraria::where('id', $propiedad->zona_horaria_id)->first();
        $pais            = $zona_horaria->nombre;
        $fecha_pais      = $enUTC->tz($pais);
        $fecha           = $fecha_pais->format('Y-m-d');
        $dia             = $fecha_pais->format('d-m-Y');

        $reservas_hoy = Reserva::whereHas('habitacion', function($query) use($id){
                    $query->where('propiedad_id', $id);
        })->where('checkout', $fecha)->with('habitacion.tipoHabitacion')->with('tipoMoneda')->with('huespedes')->with('cliente.pais', 'cliente.region')->with('estadoReserva')->whereIn('estado_reserva_id', [3,4,5])->get();

        $pdf = PDF::loadView('pdf.salidas', ['fecha' => $dia,'propiedad' => [$propiedad], 'reservas' => $reservas_hoy]);

        return $pdf->download('archivo.pdf');

    }




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

        $servicios = Servicio::where('propiedad_id', $propiedad_id)->get();

        $cantidad_servicios = [];
        $servicios_vendidos = [];
        foreach ($servicios as $servicio) {
        $cantidad_vendido   = 0;
            foreach ($pagos as $pago) {
                foreach ($pago->reserva->huespedes as $huesped) {
                    foreach ($huesped->servicios as $serv) {
                        if ($servicio->nombre == $serv->nombre) {
                            $id = $serv->pivot->id;
                            if (!in_array($id, $servicios_vendidos)) {
                                if ($serv->pivot->estado == "Pagado") {
                                    $cantidad_vendido += $serv->pivot->cantidad;
                                    array_push($servicios_vendidos, $id);
                                }
                            }
                        }
                    }
                }
            }

            $cantidad_serv['id']       = $servicio->id;
            $cantidad_serv['nombre']   = $servicio->nombre;
            $cantidad_serv['cantidad'] = $cantidad_vendido;
            array_push($cantidad_servicios, $cantidad_serv);
        }


        $auxInicio = $fecha_inicio->format('Y-m-d');
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
        })->select('checkin', 'checkout', 'estado_reserva_id')->get();
        $ingresos_total['ingresos_totales']         = $total_ingresos;
        $ingresos_total['ingresos_por_habitacion']  = $total_habitacion;
        $ingresos_total['ingresos_por_consumos']    = $total_consumos;
        $ingresos_total['cantidad_servicios']       = $cantidad_servicios;
        $ingresos_total['tipos_habitaciones']       = $ingresos_tipo_habitacion;
        $ingresos_total['tipos_fuentes']            = $ingresos_tipo_fuente;
        $ingresos_total['tipos_clientes']           = $ingresos_tipo_cliente;
        $ingresos_total['metodos_pagos']            = $ingresos_metodo_pago;


        $dt = new Carbon($request->input('fecha_inicio'));
        $mes_fecha = $dt->formatLocalized('%B');


        if ($mes_fecha == "January") {
            $mes = "Enero";
        }

        if ($mes_fecha == "February") {
            $mes = "Febrero";
        }

        if ($mes_fecha == "March") {
            $mes = "Marzo";
        }

        if ($mes_fecha == "April") {
            $mes = "Abril";
        }

        if ($mes_fecha == "May") {
            $mes = "Mayo";
        }

        if ($mes_fecha == "June") {
            $mes = "Junio";
        }

        if ($mes_fecha == "July") {
            $mes = "Julio";
        }

        if ($mes_fecha == "August") {
            $mes = "Agosto";
        }

        if ($mes_fecha == "September") {
            $mes = "Septiembre";
        }

        if ($mes_fecha == "October") {
            $mes = "Octubre";
        }

        if ($mes_fecha == "November") {
            $mes = "Noviembre";
        }

        if ($mes_fecha == "December") {
            $mes = "Diciembre";
        }

        $pdf = PDF::loadView('pdf.reporte_financiero', ['mes' => $mes ,'propiedad' => [$propiedad], 'ingresos' => $ingresos_total]);

        return $pdf->download('archivo.pdf');
 
    }


	public function estadoCuenta(Request $request)
    {

		$reservas = $request['reservas'];
		$propiedad_id = $request->input('propiedad_id');
		$cliente_id = $request->input('cliente_id');
		/*$iva = $request->input('iva');*/



		$propiedad = Propiedad::where('id', $propiedad_id)->with('pais', 'region')->get();
		$cliente = Cliente::where('id', $cliente_id)->with('pais', 'region')->get();

		$propiedad_iva = 0;
		foreach ($propiedad as $prop) {
			
			$propiedad_iva = $prop->iva;
            $propiedad_iva = $propiedad_iva / 100;

		}

		$reservas_pdf = [];
		$monto_alojamiento = 0;
		$consumo = 0;
        $iva_reservas            = null;
        $tipo_moneda_reservas    = null;
		foreach($reservas as $id){

		$reserva = Reserva::where('id', $id)->where('cliente_id', $cliente_id)->with('cliente.pais', 'cliente.region')->with('tipoMoneda')->with('habitacion.tipoHabitacion')->with('pagos.tipoMoneda', 'pagos.metodoPago', 'pagos.tipoComprobante')->with(['huespedes.servicios' => function ($q) use($id) {

        $q->wherePivot('reserva_id', $id);}])->get();


        if (count($reserva) == 0) {
          $retorno = array(
                'errors' => true,
                'msj'    => " Las reservas no pertenecen al mismo cliente"
          );
          return Response::json($retorno, 400);
        }


        foreach ($reserva as $ra) {
            if (is_null($iva_reservas)) {
                $iva_reservas = $ra->iva;
            } else {
                if ($iva_reservas != $ra->iva) {
                    $retorno = array(
                        'errors' => true,
                        'msj'    => " Error: Reservas con distinto impuesto "
                    );
                    return Response::json($retorno, 400);
                }
            }

            if (is_null($tipo_moneda_reservas)) {
                $tipo_moneda_reservas = $ra->tipo_moneda_id;
            } else {
                if ($tipo_moneda_reservas != $ra->tipo_moneda_id) {
                    $retorno = array(
                        'errors' => true,
                        'msj'    => " Error: Las reservas deben estar cursada con el mismo tipo de moneda "
                    );
                    return Response::json($retorno, 400);
                }
            }
        }



		foreach ($reserva as $ra) {
			$monto_alojamiento += $ra->monto_alojamiento;
			foreach($ra->huespedes as $huesped){
				$huesped->monto_consumo = 0;
				foreach($huesped->servicios as $servicio){
					$huesped->monto_consumo += $servicio->pivot->precio_total;
					$consumo += $servicio->pivot->precio_total;
				}
			}
		}

		array_push($reservas_pdf, $reserva);


	}

        $auxMoneda     = TipoMoneda::where('id' , $tipo_moneda_reservas)->first();
        $nombre_moneda = $auxMoneda->nombre;

        if ($tipo_moneda_reservas == 1) {

            if ($iva_reservas == 1) {
    
                $total  = $monto_alojamiento;
                $neto       = ($total / ($propiedad_iva + 1 ));
                $iva        = ($neto * $propiedad_iva);


                $pdf = PDF::loadView('pdf.estado_cuenta', ['propiedad' => $propiedad , 'cliente'=> $cliente ,'reservas_pdf'=> $reservas_pdf, 'nombre_moneda' => $nombre_moneda,'iva_reservas' => $iva_reservas, 'neto' => $neto , 'iva' => $iva, 'total' => $total]);
            
            } else {

                $total = $monto_alojamiento;
                $pdf   = PDF::loadView('pdf.estado_cuenta', ['propiedad' => $propiedad, 'cliente'=> $cliente ,'reservas_pdf'=> $reservas_pdf, 'nombre_moneda' => $nombre_moneda,'iva_reservas' => $iva_reservas,'total' => $total]);
            
            }

        }elseif($tipo_moneda_reservas == 2){

            $total = $monto_alojamiento;

            $pdf = PDF::loadView('pdf.estado_cuenta', ['propiedad' => $propiedad , 'cliente'=> $cliente ,'reservas_pdf'=> $reservas_pdf, 'nombre_moneda' => $nombre_moneda,'iva_reservas' => $iva_reservas,'total' => $total]);

        }
		

		return $pdf->download('archivo.pdf');


	}


    public function estadoCuentaResumen(Request $request)
    {

        $reservas = $request['reservas'];
        $propiedad_id = $request->input('propiedad_id');
        $cliente_id = $request->input('cliente_id');

        $propiedad = Propiedad::where('id', $propiedad_id)->with('pais', 'region')->get();
        $cliente = Cliente::where('id', $cliente_id)->with('pais', 'region')->get();

        $propiedad_iva = 0;
        foreach ($propiedad as $prop) {
            
            $propiedad_iva = $prop->iva;
            $propiedad_iva = $propiedad_iva / 100;

        }

        $reservas_pdf = [];
        $monto_alojamiento = 0;
        $consumo = 0;
        $iva_reservas            = null;
        $tipo_moneda_reservas    = null;
        foreach($reservas as $id){

        $reserva = Reserva::where('id', $id)->where('cliente_id', $cliente_id)->with('cliente.pais', 'cliente.region')->with('tipoMoneda')->with('habitacion.tipoHabitacion')->with('pagos.tipoMoneda', 'pagos.metodoPago', 'pagos.tipoComprobante')->with(['huespedes.servicios' => function ($q) use($id) {

        $q->wherePivot('reserva_id', $id);}])->get();


        if (count($reserva) == 0) {
          $retorno = array(
                'errors' => true,
                'msj'    => " Las reservas no pertenecen al mismo cliente"
          );
          return Response::json($retorno, 400);
        }


        foreach ($reserva as $ra) {
            if (is_null($iva_reservas)) {
                $iva_reservas = $ra->iva;
            } else {
                if ($iva_reservas != $ra->iva) {
                    $retorno = array(
                        'errors' => true,
                        'msj'    => " Error: Reservas con distinto impuesto "
                    );
                    return Response::json($retorno, 400);
                }
            }

            if (is_null($tipo_moneda_reservas)) {
                $tipo_moneda_reservas = $ra->tipo_moneda_id;
            } else {
                if ($tipo_moneda_reservas != $ra->tipo_moneda_id) {
                    $retorno = array(
                        'errors' => true,
                        'msj'    => " Error: Las reservas deben estar cursada con el mismo tipo de moneda "
                    );
                    return Response::json($retorno, 400);
                }
            }
        }



        foreach ($reserva as $ra) {
            $monto_alojamiento += $ra->monto_alojamiento;
            foreach($ra->huespedes as $huesped){
                $huesped->monto_consumo = 0;
                foreach($huesped->servicios as $servicio){
                    $huesped->monto_consumo += $servicio->pivot->precio_total;
                    $consumo += $servicio->pivot->precio_total;
                }
            }
        }

        array_push($reservas_pdf, $reserva);


    }

        $auxMoneda     = TipoMoneda::where('id' , $tipo_moneda_reservas)->first();
        $nombre_moneda = $auxMoneda->nombre;

        if ($tipo_moneda_reservas == 1) {

            if ($iva_reservas == 1) {
                $total         = $monto_alojamiento;
                $neto          = ($total / ($propiedad_iva + 1 ));
                $iva           = ($neto * $propiedad_iva);

                $pdf = PDF::loadView('pdf.estado_cuenta_resumen', ['propiedad' => $propiedad , 'cliente'=> $cliente ,'reservas_pdf'=> $reservas_pdf, 'nombre_moneda' => $nombre_moneda,'iva_reservas' => $iva_reservas, 'neto' => $neto , 'iva' => $iva, 'total' => $total]);
            
            } else {

                $total = $monto_alojamiento;
                $pdf   = PDF::loadView('pdf.estado_cuenta_resumen', ['propiedad' => $propiedad, 'cliente'=> $cliente ,'reservas_pdf'=> $reservas_pdf, 'nombre_moneda' => $nombre_moneda,'iva_reservas' => $iva_reservas,'total' => $total]);
            
            }

        }elseif($tipo_moneda_reservas == 2){

            $total = $monto_alojamiento;

            $pdf = PDF::loadView('pdf.estado_cuenta_resumen', ['propiedad' => $propiedad , 'cliente'=> $cliente ,'reservas_pdf'=> $reservas_pdf, 'nombre_moneda' => $nombre_moneda,'iva_reservas' => $iva_reservas,'total' => $total]);

        }
        

        return $pdf->download('archivo.pdf');


    }

    public function comprobanteReserva(Request $request)
    {

        $reservas     = $request['reservas'];
        $propiedad_id = $request->input('propiedad_id');
        $cliente_id   = $request->input('cliente_id');
        $propiedad    = Propiedad::where('id', $propiedad_id)->with('pais', 'region')->with('politicas')->get();
        $cliente      = Cliente::where('id', $cliente_id)->with('pais', 'region')->get();

        $propiedad_iva = 0;
        foreach ($propiedad as $prop) {
            
            $propiedad_iva = $prop->iva;
            $propiedad_iva = $propiedad_iva / 100;

        }

        $reservas_pdf = [];
        $monto_alojamiento = 0;
        $consumo = 0;
        $por_pagar = 0;
        $iva_reservas            = null;
        $tipo_moneda_reservas    = null;
        foreach($reservas as $id){

        $reserva = Reserva::where('id', $id)->where('cliente_id', $cliente_id)->with('cliente.pais', 'cliente.region')->with('tipoMoneda')->with('habitacion.tipoHabitacion')->with('pagos.tipoMoneda', 'pagos.metodoPago', 'pagos.tipoComprobante')->with(['huespedes.servicios' => function ($q) use($id) {

        $q->wherePivot('reserva_id', $id);}])->get();


        if (count($reserva) == 0) {
          $retorno = array(
                'errors' => true,
                'msj'    => " Las reservas no pertenecen al mismo cliente"
          );
          return Response::json($retorno, 400);
        }


        foreach ($reserva as $ra) {
            if (is_null($iva_reservas)) {
                $iva_reservas = $ra->iva;
            } else {
                if ($iva_reservas != $ra->iva) {
                    $retorno = array(
                        'errors' => true,
                        'msj'    => " Error: Reservas con distinto impuesto "
                    );
                    return Response::json($retorno, 400);
                }
            }

            if (is_null($tipo_moneda_reservas)) {
                $tipo_moneda_reservas = $ra->tipo_moneda_id;
            } else {
                if ($tipo_moneda_reservas != $ra->tipo_moneda_id) {
                    $retorno = array(
                        'errors' => true,
                        'msj'    => " Error: Las reservas deben estar cursada con el mismo tipo de moneda "
                    );
                    return Response::json($retorno, 400);
                }
            }
        }



        foreach ($reserva as $ra) {
            $por_pagar += $ra->monto_por_pagar;
            $monto_alojamiento += $ra->monto_alojamiento;
            foreach($ra->huespedes as $huesped){
                $huesped->monto_consumo = 0;
                foreach($huesped->servicios as $servicio){
                    $huesped->monto_consumo += $servicio->pivot->precio_total;
                    $consumo += $servicio->pivot->precio_total;
                }
            }
        }

        array_push($reservas_pdf, $reserva);


    }

        $auxMoneda     = TipoMoneda::where('id' , $tipo_moneda_reservas)->first();
        $nombre_moneda = $auxMoneda->nombre;

        if ($tipo_moneda_reservas == 1) {

            if ($iva_reservas == 1) {
                $total         = $monto_alojamiento;
                $neto          = ($total / ($propiedad_iva + 1 ));
                $iva           = ($neto * $propiedad_iva);

                $pdf = PDF::loadView('pdf.comprobante_reserva', ['propiedad' => $propiedad , 'cliente'=> $cliente ,'reservas_pdf'=> $reservas_pdf, 'nombre_moneda' => $nombre_moneda,'iva_reservas' => $iva_reservas, 'neto' => $neto , 'iva' => $iva, 'total' => $total, 'por_pagar' => $por_pagar]);
            
            } else {

                $total = $monto_alojamiento;
                $pdf   = PDF::loadView('pdf.comprobante_reserva', ['propiedad' => $propiedad, 'cliente'=> $cliente ,'reservas_pdf'=> $reservas_pdf, 'nombre_moneda' => $nombre_moneda,'iva_reservas' => $iva_reservas,'total' => $total,'por_pagar' => $por_pagar]);
            
            }

        }elseif($tipo_moneda_reservas == 2){

            $total = $monto_alojamiento;

            $pdf = PDF::loadView('pdf.comprobante_reserva', ['propiedad' => $propiedad , 'cliente'=> $cliente ,'reservas_pdf'=> $reservas_pdf, 'nombre_moneda' => $nombre_moneda,'iva_reservas' => $iva_reservas,'total' => $total,'por_pagar' => $por_pagar]);

        }
        

        return $pdf->download('archivo.pdf');


    }

    public function comprobanteReservaResumen(Request $request)
    {

        $reservas     = $request['reservas'];
        $propiedad_id = $request->input('propiedad_id');
        $cliente_id   = $request->input('cliente_id');
        $propiedad    = Propiedad::where('id', $propiedad_id)->with('pais', 'region')->get();
        $cliente      = Cliente::where('id', $cliente_id)->with('pais', 'region')->get();

        $propiedad_iva = 0;
        foreach ($propiedad as $prop) {
            
            $propiedad_iva = $prop->iva;
            $propiedad_iva = $propiedad_iva / 100;

        }

        $reservas_pdf = [];
        $monto_alojamiento = 0;
        $consumo = 0;
        $por_pagar = 0;
        $iva_reservas            = null;
        $tipo_moneda_reservas    = null;
        foreach($reservas as $id){

        $reserva = Reserva::where('id', $id)->where('cliente_id', $cliente_id)->with('cliente.pais', 'cliente.region')->with('tipoMoneda')->with('habitacion.tipoHabitacion')->with('pagos.tipoMoneda', 'pagos.metodoPago', 'pagos.tipoComprobante')->with(['huespedes.servicios' => function ($q) use($id) {

        $q->wherePivot('reserva_id', $id);}])->get();


        if (count($reserva) == 0) {
          $retorno = array(
                'errors' => true,
                'msj'    => " Las reservas no pertenecen al mismo cliente"
          );
          return Response::json($retorno, 400);
        }


        foreach ($reserva as $ra) {
            if (is_null($iva_reservas)) {
                $iva_reservas = $ra->iva;
            } else {
                if ($iva_reservas != $ra->iva) {
                    $retorno = array(
                        'errors' => true,
                        'msj'    => " Error: Reservas con distinto impuesto "
                    );
                    return Response::json($retorno, 400);
                }
            }

            if (is_null($tipo_moneda_reservas)) {
                $tipo_moneda_reservas = $ra->tipo_moneda_id;
            } else {
                if ($tipo_moneda_reservas != $ra->tipo_moneda_id) {
                    $retorno = array(
                        'errors' => true,
                        'msj'    => " Error: Las reservas deben estar cursada con el mismo tipo de moneda "
                    );
                    return Response::json($retorno, 400);
                }
            }
        }



        foreach ($reserva as $ra) {
            $por_pagar += $ra->monto_por_pagar;
            $monto_alojamiento += $ra->monto_alojamiento;
            foreach($ra->huespedes as $huesped){
                $huesped->monto_consumo = 0;
                foreach($huesped->servicios as $servicio){
                    $huesped->monto_consumo += $servicio->pivot->precio_total;
                    $consumo += $servicio->pivot->precio_total;
                }
            }
        }

        array_push($reservas_pdf, $reserva);


    }

        $auxMoneda     = TipoMoneda::where('id' , $tipo_moneda_reservas)->first();
        $nombre_moneda = $auxMoneda->nombre;

        if ($tipo_moneda_reservas == 1) {

            if ($iva_reservas == 1) {
                $total         = $monto_alojamiento;
                $neto          = ($total / ($propiedad_iva + 1 ));
                $iva           = ($neto * $propiedad_iva);

                
                $pdf = PDF::loadView('pdf.comprobante_reserva_resumen', ['propiedad' => $propiedad , 'cliente'=> $cliente ,'reservas_pdf'=> $reservas_pdf, 'nombre_moneda' => $nombre_moneda,'iva_reservas' => $iva_reservas, 'neto' => $neto , 'iva' => $iva, 'total' => $total, 'por_pagar' => $por_pagar]);
            
            } else {

                $total = $monto_alojamiento;
                $pdf   = PDF::loadView('pdf.comprobante_reserva_resumen', ['propiedad' => $propiedad, 'cliente'=> $cliente ,'reservas_pdf'=> $reservas_pdf, 'nombre_moneda' => $nombre_moneda,'iva_reservas' => $iva_reservas,'total' => $total,'por_pagar' => $por_pagar]);
            
            }

        }elseif($tipo_moneda_reservas == 2){

            $total = $monto_alojamiento;

            $pdf = PDF::loadView('pdf.comprobante_reserva_resumen', ['propiedad' => $propiedad , 'cliente'=> $cliente ,'reservas_pdf'=> $reservas_pdf, 'nombre_moneda' => $nombre_moneda,'iva_reservas' => $iva_reservas,'total' => $total,'por_pagar' => $por_pagar]);

        }
        

        return $pdf->download('archivo.pdf');


    }


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

        if ($request->has('tipo_comprobante_id')) {
            $tipos_comprobante = $request->get('tipo_comprobante_id');

            $pago->whereHas('reserva.habitacion', function($query) use($propiedad_id){
                $query->where('propiedad_id', $propiedad_id);
            })->where(function ($query) use ($tipos_comprobante) {
                $query->where(function ($query) use ($tipos_comprobante) {
                $query->whereIn('tipo_comprobante_id', $tipos_comprobante);
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
            $fechas[$i] = ['fecha' => $fecha, 'moneda' => $montos, 'ps' => []];

            $auxFecha->addDay();
        }

        $ini  = new Carbon($request->input('fecha_inicio'));
        $inc  = $ini->startOfDay();

        foreach ($pagos as $pago) {
            $created_at  = new Carbon($pago->created_at);
            $crat        = $created_at->startOfDay();
            $dif         = $inc->diffInDays($crat); 
            $largo       = sizeof($fechas[$dif]['moneda']);
            array_push($fechas[$dif]['ps'], $pago);

            for( $i = 0 ; $i < $largo ; $i++){
                if ($fechas[$dif]['moneda'][$i]['id'] == $pago->tipo_moneda_id ){
                    $fechas[$dif]['moneda'][$i]['suma'] += $pago->monto_equivalente;
                }
            }
        }

        $fechas_montos = [];
        foreach ($fechas as $fecha) {
            $largo = count($fecha) - 1;
            for( $i = 0 ; $i < $largo ; $i++){
                if ($fecha['moneda'][$i]['suma'] != 0 ){
                    if (!in_array($fecha, $fechas_montos)) {
                        array_push($fechas_montos, $fecha);
                    }
                }
            }
        }

        $montos_totales = [];
        foreach ($propiedad->tipoMonedas as $moneda) {
            $suma = 0;
            foreach ($fechas_montos as $fechas) {
                foreach ($fechas['moneda'] as $m) {
                    if ($m['nombre'] == $moneda->nombre) {
                        $suma += $m['suma'];
                    }
                }
            }
            $total['nombre_moneda']  = $moneda->nombre;
            $total['monto']          = $suma;
            array_push($montos_totales, $total);

        }
        
        $pdf = PDF::loadView('pdf.pagos', ['propiedad' => [$propiedad], 'fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin, 'fechas' => $fechas_montos, 'ingresos_totales' => $montos_totales]);

        return $pdf->download('archivo.pdf');

    }

    public function checkin(Request $request)
    {
        $reservas = $request['reservas'];
        $propiedad_id = $request->input('propiedad_id');
        $cliente_id = $request->input('cliente_id');

        $propiedad = Propiedad::where('id', $propiedad_id)->with('pais', 'region')->get();
        $cliente = Cliente::where('id', $cliente_id)->with('pais', 'region')->get();

        foreach ($propiedad as $prop) {
          
          $propiedad_iva = $prop->iva;
          $propiedad_iva = $propiedad_iva / 100;

        }

        $reservas_pdf = [];
        $monto_alojamiento = 0;

        $iva_reservas            = null;
        $tipo_moneda_reservas    = null;
        foreach($reservas as $id){
            $reserva = Reserva::where('id', $id)->where('cliente_id', $cliente_id)->with('cliente.pais', 'cliente.region')->with('tipoMoneda')->with('habitacion.tipoHabitacion')->first();

            if (is_null($reserva)) {
              $retorno = array(
                    'errors' => true,
                    'msj'    => " Las reservas no pertenecen al mismo cliente"
                );
              return Response::json($retorno, 400);
            }

            if (is_null($iva_reservas)) {
                $iva_reservas = $reserva->iva;
            } else {
                if ($iva_reservas != $reserva->iva) {
                    $retorno = array(
                        'errors' => true,
                        'msj'    => " Error: Reservas con distinto impuesto "
                    );
                    return Response::json($retorno, 400);
                }
            }

            if (is_null($tipo_moneda_reservas)) {
                $tipo_moneda_reservas = $reserva->tipo_moneda_id;
            } else {
                if ($tipo_moneda_reservas != $reserva->tipo_moneda_id) {
                    $retorno = array(
                        'errors' => true,
                        'msj'    => " Error: Las reservas deben estar cursada con el mismo tipo de moneda "
                    );
                    return Response::json($retorno, 400);
                }
            }

        $monto_alojamiento += $reserva->monto_alojamiento;

        array_push($reservas_pdf, $reserva);

        }

        $auxMoneda     = TipoMoneda::where('id' , $tipo_moneda_reservas)->first();
        $nombre_moneda = $auxMoneda->nombre;


        if ($tipo_moneda_reservas == 1) {

            if ($iva_reservas == 1) {
                $total         = $monto_alojamiento;
                $neto          = ($total / ($propiedad_iva + 1 ));
                $iva           = ($neto * $propiedad_iva);

                $pdf = PDF::loadView('pdf.checkin', ['propiedad' => $propiedad , 'cliente'=> $cliente ,'reservas_pdf'=> [$reservas_pdf], 'nombre_moneda' => $nombre_moneda,'iva_reservas' => $iva_reservas, 'neto' => $neto , 'iva' => $iva, 'total' => $total]);
            
            } else {

                $total = $monto_alojamiento;
                $pdf   = PDF::loadView('pdf.checkin', ['propiedad' => $propiedad, 'cliente'=> $cliente ,'reservas_pdf'=> [$reservas_pdf], 'nombre_moneda' => $nombre_moneda,'iva_reservas' => $iva_reservas,'total' => $total]);
            
            }

        }elseif($tipo_moneda_reservas == 2){

            $total = $monto_alojamiento;

            $pdf = PDF::loadView('pdf.checkin', ['propiedad' => $propiedad , 'cliente'=> $cliente ,'reservas_pdf'=> [$reservas_pdf], 'nombre_moneda' => $nombre_moneda,'iva_reservas' => $iva_reservas,'total' => $total]);

        }
        

        return $pdf->download('archivo.pdf');

    }

    public function huesped(Request $request)
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

        $fecha        = Carbon::today()->format('d-m-Y');
        $fecha_actual = Carbon::today()->format('Y-m-d');
        $propiedad_id = $request->input('propiedad_id');
        $propiedad    = Propiedad::where('id', $request->input('propiedad_id'))->with('pais')->first();

        $habitaciones = Habitacion::where('propiedad_id', $propiedad_id)->with(['reservas' => function ($q) use($fecha_actual){
            $q->where('estado_reserva_id', 3)->where('checkin', '<=', $fecha_actual)->where('checkout', '>=', $fecha_actual)->with('huespedes');
        }])->get();

        foreach ($habitaciones as $habitacion) {
            if (count($habitacion->reservas) == 0) {
                $habitacion->estado = "Disponible";
            } else {
                $habitacion->estado = "Ocupada";
            }
        }

        $pdf = PDF::loadView('pdf.huesped', ['propiedad' => [$propiedad], 'fecha' =>  $fecha ,'habitaciones' => $habitaciones]);

        return $pdf->download('archivo.pdf');
        
    } 


	public function reporte(Request $request)
	{

		    if($request->has('propiedad_id')){

            $propiedad_id = $request->input('propiedad_id');
            $propiedad = Propiedad::where('id', $request->input('propiedad_id'))->with('pais')->first();

            if(!is_null($propiedad)){

            $inicio       = new Carbon($request->input('fecha_inicio'));
            $zona_horaria = ZonaHoraria::where('id', $propiedad->zona_horaria_id)->first();
            $pais         = $zona_horaria->nombre;
            $fecha_inicio = Carbon::createFromFormat('Y-m-d H:i:s', $inicio, $pais)->tz('UTC');


            if ($request->has('fecha_fin')) {
            
            $fin             = new Carbon($request->input('fecha_fin'));
            $fechaFin        = $fin->addDay();
            $fin_fecha       = $fechaFin->startOfDay();
            $fecha_fin       = Carbon::createFromFormat('Y-m-d H:i:s', $fechaFin, $pais)->tz('UTC');


            }else{

            $fecha_fin    = Carbon::createFromFormat('Y-m-d H:i:s', $inicio, $pais)->tz('UTC')->addDay();

            }


                    
                    $pagos = Pago::where('created_at','>=' , $fecha_inicio)->where('created_at', '<' , $fecha_fin)->whereHas('reserva.habitacion', function($query) use($propiedad_id){

                    $query->where('propiedad_id', $propiedad_id);

                    })->get();


                    $reservas_creadas = Reserva::where('created_at' , '>=', $fecha_inicio)->where('created_at', '<' , $fecha_fin)->whereHas('habitacion', function($query) use($propiedad_id){

                    $query->where('propiedad_id', $propiedad_id);

                    })->get();

                    $auxInicio = $inicio->format('Y-m-d');
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
                        /*$fecha = $auxFecha_inicio->format('Y-m-d');*/

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
                    
                    $inicio_fecha = $inicio->format('d-m-Y');
                    $fin_fecha = $auxFecha_fin->subDay()->format('d-m-Y');

                    $fechas = ['inicio' => $inicio_fecha, 'fin' => $fin_fecha];

                  $pdf = PDF::loadView('pdf.reporte_diario', ['propiedad' => [$propiedad], 'fechas' => $fechas ,'reservas_realizadas'=> count($reservas_creadas),'reservas_anuladas' => count($reservas_anuladas), 'reservas_no_show' => count($reservas_no_show), 'ingresos_habitacion' => $ingresos_habitacion, 'ingresos_consumo' => $ingresos_consumos, 'ingresos_totales' => $ingresos_totales_dia, 'residentes_locales' => $residentes_pais_propiedad, 'residentes_extranjero' => $residentes_extranjero, 'ocupado' => $suma , 'disponible' => ($total_noches - $suma)]);


				return $pdf->download('archivo.pdf');



            }else{

                
                $retorno = array(

                    'msj'    => "Propiedad no encontrada",
                    'errors' => true,

                );

                return Response::json($retorno, 404);


            }




        }



	}






}