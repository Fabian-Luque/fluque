<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Propiedad;
use App\Temporada;
use App\TipoHabitacion;
use App\TipoMoneda;
use App\Calendario;
use App\Reserva;
use App\Habitacion;
use App\Cliente;
use App\ColorMotor;
use App\ClasificacionColor;
use App\MotorPropiedad;
use Response;
use Validator;
use \Carbon\Carbon;
use App\ZonaHoraria;
use JWTAuth;
use App\Events\ReservasMapaEvent;
use Illuminate\Support\Facades\Event;


class ReservaMapaController extends Controller
{
    public function getDisponibilidad(Request $request)
	{
        if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
            $inicio = new Carbon($request->input('fecha_inicio'));
            $fin    = new Carbon($request->input('fecha_fin'));
        } else {
            $data = array(
                'msj'    => "No se envian fechas",
                'errors' => true,);
            return Response::json($data, 400);
        }
        if ($request->has('propiedad_id')) {
            $propiedad_id   = $request->input('propiedad_id');
            $propiedad      = Propiedad::where('id', $propiedad_id)->with('tiposHabitacion')->with('tipoMonedas')->with('cuentasBancaria.tipoCuenta', 'tipoDepositoPropiedad.tipoDeposito')->with('politicas')->first();
            if (is_null($propiedad)) {
                $retorno  = array(
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

        $propiedad_id 			   = $propiedad->id;
        $propiedad_monedas         = $propiedad->tipoMonedas; // monedas propiedad
        $tipo_habitacion_propiedad = $propiedad->tiposHabitacion;

        if ($inicio < $fin) {
            
            $fecha_inicio = $inicio->startOfDay()->format('Y-m-d');
            $fecha_fin    = $fin->startOfDay()->format('Y-m-d');

            $habitaciones_disponibles = Habitacion::where('propiedad_id', $propiedad_id)
            ->whereDoesntHave('reservas', function ($query) use ($fecha_inicio, $fecha_fin) {
                $query->whereIn('estado_reserva_id', [1,2,3,4,5])
                ->where(function ($query) use ($fecha_inicio, $fecha_fin) {
                    $query->where(function ($query) use ($fecha_inicio, $fecha_fin) {
                        $query->where('checkin', '>=', $fecha_inicio);
                        $query->where('checkin', '<',  $fecha_fin);
                    });
                    $query->orWhere(function($query) use ($fecha_inicio,$fecha_fin){
                        $query->where('checkin', '<=', $fecha_inicio);
                        $query->where('checkout', '>',  $fecha_inicio);
                    });                
                });
            })
            ->with('tipoHabitacion')
            ->get();

            $tipos_habitacion = [];
            foreach ($tipo_habitacion_propiedad as $tipo) {

                $reservas = Reserva::where(function ($query) use ($fecha_inicio, $fecha_fin) {
                    $query->where(function ($query) use ($fecha_inicio, $fecha_fin) {
                        $query->where('checkin', '>=', $fecha_inicio);
                        $query->where('checkin', '<',  $fecha_fin);
                    });
                    $query->orWhere(function($query) use ($fecha_inicio,$fecha_fin){
                        $query->where('checkin', '<=', $fecha_inicio);
                        $query->where('checkout', '>',  $fecha_inicio);
                    });                
                })
                ->where('tipo_habitacion_id', $tipo->id)
                ->where('habitacion_id', null)
                ->whereIn('estado_reserva_id', [1,2,3,4,5])
                ->get();

                $venta_propiedad = $tipo->venta_propiedad;
                $cantidad_disponibles = 0;
                foreach ($habitaciones_disponibles as $habitacion) {
                    if ($habitacion->tipo_habitacion_id == $tipo->id) {
                        $cantidad_disponibles += 1;
                    }
                }

                if ($venta_propiedad <= $cantidad_disponibles) {
                    $disponibles = $venta_propiedad;
                } elseif($venta_propiedad > $cantidad_disponibles) {
                    $disponibles = $cantidad_disponibles;
                }

                $disponibles = $disponibles - count($reservas);
                if ($disponibles > 0) {
                    $tipo->cantidad_disponibles = $disponibles;
                    array_push($tipos_habitacion, $tipo);
                }

            }

            $fechaInicio            = new Carbon($request->input('fecha_inicio'));
            $fechaFin               = new Carbon($request->input('fecha_fin'));
            $propiedad_monedas      = $propiedad->tipoMonedas; // monedas propiedad

            foreach ($tipos_habitacion as $tipo_habitacion) {
                $precios                    = $tipo_habitacion->precios;
                $tipo_habitacion_id         = $tipo_habitacion->id;
                $capacidad                  = $tipo_habitacion->capacidad;
                $precio_promedio_habitacion = [];
                $auxPrecio                  = [];
                $auxFecha                   = new Carbon($fechaInicio);

                while ($auxFecha < $fechaFin) {

                    $temporada = Temporada::where('propiedad_id', $propiedad_id)->whereHas('calendarios', function ($query) use ($auxFecha) {
                        $query->where('fecha', $auxFecha);})->first();

                    if (!is_null($temporada)) {
                        $temporada_id      = $temporada->id;
                        $precios_temporada = $precios->where('temporada_id', $temporada_id)->where('tipo_habitacion_id', $tipo_habitacion_id);
                        if ($propiedad->tipo_cobro_id != 3) {
                            foreach ($propiedad_monedas as $moneda) {
                                $tipo_moneda = $moneda->id;
                                foreach ($precios_temporada as $precio) {
                                    if ($tipo_moneda == $precio->tipo_moneda_id) {
                                        $precio_tipo_habitacion['cantidad_huespedes'] = $precio->cantidad_huespedes;
                                        $precio_tipo_habitacion['precio']             = $precio->precio;
                                        $precio_tipo_habitacion['tipo_moneda_id']     = $moneda->id;
                                        $precio_tipo_habitacion['nombre_moneda']      = $moneda->nombre;
                                        $precio_tipo_habitacion['cantidad_decimales'] = $moneda->cantidad_decimales;
                                        array_push($auxPrecio, $precio_tipo_habitacion);
                                    }
                                }
                            }
                        } else {
                            foreach ($propiedad_monedas as $moneda) {
                                $tipo_moneda = $moneda->id;
                                foreach ($precios_temporada as $precio) {
                                    if ($tipo_moneda == $precio->tipo_moneda_id) {
                                        $precio_tipo_habitacion['cantidad_huespedes'] = $precio->cantidad_huespedes;
                                        $precio_tipo_habitacion['precio']             = $precio->precio;
                                        $precio_tipo_habitacion['tipo_moneda_id']     = $moneda->id;
                                        $precio_tipo_habitacion['nombre_moneda']      = $moneda->nombre;
                                        $precio_tipo_habitacion['cantidad_decimales'] = $moneda->cantidad_decimales;
                                        array_push($auxPrecio, $precio_tipo_habitacion);
                                    }
                                }
                            }
                        }
                    } else {
                        $data = array(
                            'msj'    => "Debe configurar una temporada para las fechas seleccionadas ",
                            'errors' => true,);
                        return Response::json($data, 400);
                    }
                    $auxFecha->addDay();

                }

                if ($propiedad->tipo_cobro_id != 3) {
                    foreach ($propiedad_monedas as $moneda) {
                        $moneda_id  = $moneda->id;
                        $sumaPrecio = 0;
                        foreach ($auxPrecio as $precio_habitacion) {
                            if ($precio_habitacion['tipo_moneda_id'] == $moneda_id && $precio_habitacion['cantidad_huespedes'] == 1) {
                                $sumaPrecio += $precio_habitacion['precio'];
                            }
                        }
                        $precio_promedio['cantidad_huespedes'] = 1;
                        $precio_promedio['precio']             = $sumaPrecio;
                        $precio_promedio['tipo_moneda_id']     = $moneda->id;
                        $precio_promedio['nombre_moneda']      = $moneda->nombre;
                        $precio_promedio['cantidad_decimales'] = $moneda->cantidad_decimales;
                        array_push($precio_promedio_habitacion, $precio_promedio);
                    }
                } else {
                    for ($i=1; $i<=$capacidad ; $i++) {
                        foreach ($propiedad_monedas as $moneda) {
                            $moneda_id  = $moneda->id;
                            $sumaPrecio = 0;
                            foreach ($auxPrecio as $precio_habitacion) {
                                if ($precio_habitacion['tipo_moneda_id'] == $moneda_id && $precio_habitacion['cantidad_huespedes'] == $i) {
                                    $sumaPrecio += $precio_habitacion['precio'];
                                }
                            }
                            $precio_promedio['cantidad_huespedes'] = $i;
                            $precio_promedio['precio']             = $sumaPrecio;
                            $precio_promedio['tipo_moneda_id']     = $moneda->id;
                            $precio_promedio['nombre_moneda']      = $moneda->nombre;
                            $precio_promedio['cantidad_decimales'] = $moneda->cantidad_decimales;
                            array_push($precio_promedio_habitacion, $precio_promedio);
                        }
                    }
                }

                $tipo_habitacion->precio = $precio_promedio_habitacion;

            }

            $hab_disponibles = [];
            foreach ($tipos_habitacion as $tipo) {
                if ($tipo->venta_propiedad > 0) {
                    array_push($hab_disponibles, $tipo);
                }
            }
            $data['nombre']             = $propiedad->nombre;
            $data['direccion']          = $propiedad->direccion;
            $data['telefono']           = $propiedad->telefono;
            $data['email']              = $propiedad->email;
            $data['ciudad']             = $propiedad->ciudad;
            $data['tipo_cobro_id']      = $propiedad->tipo_cobro_id;
            $data['tipo_monedas']       = $propiedad->tipoMonedas;
            $data['cuentas_bancaria']   = $propiedad->cuentasBancaria;
            $data['politicas']          = $propiedad->politicas;
            $data['tipo_deposito']      = $propiedad->tipoDepositoPropiedad;
            $data['tipos_habitaciones'] = $hab_disponibles;
            return $data;

        } else {
            $retorno = array(
                'msj'    => "Las fechas no corresponden",
                'errors' => true,);
            return Response::json($retorno, 400);
        }

    }

    

    public function reserva(Request $request)
    {
        $propiedad_id = null;
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

        if ($request->has('tipo_moneda_id') && $request->has('fecha_inicio') && $request->has('fecha_fin') && $request->has('iva') && $request->has('noches') && $request->has('habitaciones') && $request->has('cliente')) {
            $tipo_moneda_id = $request->get('tipo_moneda_id');
            $fecha_inicio   = $request->get('fecha_inicio');
            $fecha_fin      = $request->get('fecha_fin');
            $iva            = $request->get('iva');
            $noches         = $request->get('noches');
            $clientes       = $request['cliente'];
            $habitaciones   = $request['habitaciones'];

            if (!is_array($habitaciones)) {
                $habitaciones = [];
                $habitaciones . push($request['habitaciones']);
            }

            $reservas = Reserva::whereHas('tipoHabitacion', function($query) use($propiedad_id){
                $query->where('propiedad_id', $propiedad_id);
            })
            ->where('tipo_fuente_id', 9)
            ->whereIn('estado_reserva_id', [1,2,3,4,5])
            ->orderby('n_reserva_propiedad', 'DESC')
            ->get();

            $reserva  = $reservas->first();
            if (!is_null($reserva)) {
                $n_reserva_propiedad = $reserva->n_reserva_propiedad;
            } else {
                $n_reserva_propiedad = 0;
            }

            if ($clientes['tipo_cliente_id'] == 1) {
                if ($request->has('cliente.rut')) {
                    $cliente                         = Cliente::firstOrNew($request['cliente']);
                    $cliente->tipo_cliente_id        = $clientes['tipo_cliente_id'];
                    $cliente->nombre                 = $clientes['nombre'];
                    $cliente->apellido               = $clientes['apellido'];
                    $cliente->giro                   = null;
                    $cliente->save();
                } else {
                    $cliente                         = new Cliente();
                    $cliente->nombre                 = $clientes['nombre'];
                    $cliente->apellido               = $clientes['apellido'];
                    if ($request->has('cliente.direccion')) {
                        $cliente->direccion          = $clientes['direccion'];
                    } else {
                        $cliente->direccion          = null;
                    }
                    if ($request->has('cliente.ciudad')) {
                        $cliente->ciudad             = $clientes['ciudad'];
                    } else {
                        $cliente->ciudad             = null;
                    }
                    if ($request->has('cliente.telefono')) {
                        $cliente->telefono           = $clientes['telefono'];
                    } else {
                        $cliente->telefono           = null;
                    }
                    if ($request->has('cliente.email')) {
                        $cliente->email              = $clientes['email'];
                    } else {
                        $cliente->email              = null;
                    }
                    if ($request->has('cliente.pais_id')) {
                        $cliente->pais_id            = $clientes['pais_id'];
                    } else {
                        $cliente->pais_id            = null;
                    }
                    if ($request->has('cliente.region_id')) {
                        $cliente->region_id          = $clientes['region_id'];
                    } else {
                        $cliente->region_id          = null;
                    }
                    $cliente->tipo_cliente_id        = $clientes['tipo_cliente_id'];
                    $cliente->save();
                }
            } else {
                if ($clientes['tipo_cliente_id'] == 2) {
                    $cliente                    = Cliente::firstOrNew($request['cliente']);
                    $cliente->rut               = $clientes['rut'];
                    $cliente->nombre            = $clientes['nombre'];
                    $cliente->tipo_cliente_id   = $clientes['tipo_cliente_id'];
                    $cliente->save();
                }
            }

            foreach ($habitaciones as $habitacion) {
                $reserva                        = new Reserva();
                $reserva->monto_alojamiento     = $habitacion['monto_alojamiento'];
                $reserva->monto_total           = $habitacion['monto_alojamiento'];
                $reserva->monto_consumo         = 0;
                $reserva->monto_por_pagar       = $habitacion['monto_alojamiento'];
                $reserva->ocupacion             = $habitacion['ocupacion'];
                $reserva->tipo_fuente_id        = 9;
                $reserva->cliente_id            = $cliente->id;
                $reserva->checkin               = $fecha_inicio;
                $reserva->checkout              = $fecha_fin;
                $reserva->tipo_moneda_id        = $tipo_moneda_id;
                $reserva->iva                   = $iva;
                $reserva->estado_reserva_id     = 1;
                $reserva->noches                = $request['noches'];
                $reserva->tipo_habitacion_id    = $habitacion['tipo_habitacion_id'];
                $reserva->observacion           = $request['observacion'];
                $reserva->monto_deposito        = $habitacion['monto_deposito'];
                $reserva->n_reserva_propiedad   = $n_reserva_propiedad + 1;
                $reserva->save();
            }

            Event::fire(
                new ReservasMapaEvent($propiedad_id)
            );

            $arr = array(
                'propiedad' => $propiedad
            );

            $this->EnvioCorreo(
                $propiedad,
                $cliente->email,
                $arr,
                "correos.aviso_reserva_motor",
                "",
                "",
                1,
                "",
                ""
            );
        } else {
            $retorno = array(
                'msj'    => "Incompleto",
                'errors' => true);
            return Response::json($retorno, 400);
        }
        
        $retorno = array(
            'msj'       => "Reserva creada satisfactoriamente",
            'errors'    => false);
        return Response::json($retorno, 201);

    }

}
