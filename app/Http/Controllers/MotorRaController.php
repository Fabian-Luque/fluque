<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\ReservasMotorEvent;
use Illuminate\Support\Facades\Event;
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
use Illuminate\Support\Facades\Validator;
use \Carbon\Carbon;
use App\ZonaHoraria;
use JWTAuth;
use Storage;
use Aws\S3\Exception\S3Exception;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Html;

class MotorRaController extends Controller {
    public function UploadImage(Request $request) {
        try {
            if ( $request->has('nombre') &&  $request->has('nombre_prop')) {
                $validator = Validator::make(
                    $request->all(), 
                    array(
                        'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    )
                );

                $imageName = time().'.'.$request->image->getClientOriginalExtension();
                $image = $request->file('image');
                $t = Storage::disk('s3')->put(
                    $imageName, 
                    file_get_contents($image), 
                    'public'
                );
                $imageName = Storage::disk('s3')->url($imageName);

                $retorno['error'] = true;
                $retorno['msj'] = 'Upload exitoso';
                $retorno['img'] = 'https://s3-sa-east-1.amazonaws.com/gofeels-props-images/'.$request->nombre_prop."/".$request->nombre;
            } else {
                $retorno['error'] = true;
                $retorno['msj'] = "Datos requeridos";
            }
        } catch (S3Exception $e) {
            $retorno['error'] = true;
            $retorno['msj'] = $e->getMessage();
        } 
        return Response::json($retorno);
    }

    public function GetAllImagesByDir(Request $request) {
        if ($request->has('nombre_prop') && $request->has('nombre')) {
            try {
                $retorno['error'] = false;
                $retorno['msj'] = "Listado de imagenes";
                $retorno['img'] = "https://s3-sa-east-1.amazonaws.com/gofeels-props-images/".$request->nombre_prop."/".$request->nombre;
            } catch (S3Exception $e) {
                $retorno['error'] = true;
                $retorno['msj'] = $e->getMessage();
            }
        } elseif ($request->has('nombre_prop')) {
            try {
                $retorno['error'] = false;
                $retorno['msj'] = "Listado de imagenes";
                $retorno['url'] = "https://s3-sa-east-1.amazonaws.com/gofeels-props-images/";
                $retorno['lista'] = Storage::disk('s3')->allFiles(
                    $request->nombre_prop
                );
            } catch (S3Exception $e) {
                $retorno['error'] = true;
                $retorno['msj'] = $e->getMessage();
            } 
        } else {
            $retorno['error'] = true;
            $retorno['msj'] = "Datos requeridos";
        }
        return Response::json($retorno);
    }

    public function GetImage(Request $request) {
        try {
            if ( $request->has('name') &&  $request->has('nombre_prop')) {
                $image = Storage::disk('s3')->get(
                    $request->nombre_prop."/".$request->name
                );
                $response = new Response($image, 200, [
                    'Content-Type' => $attachment->type,
                ]);
                $retorno['error'] = true;
                $retorno['msj'] = $response;


            } else {
                $retorno['error'] = true;
                $retorno['msj'] = "Datos requeridos";
            }
        } catch (S3Exception $e) {
            $retorno['error'] = true;
            $retorno['msj'] = "Error: ".$e->getMessage();
        } catch (FileNotFoundException $e) {
            $retorno['error'] = true;
            $retorno['msj'] = "Error: ".$e->getMessage();
        }
        return Response::json($retorno);
    }

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
        if ($request->has('codigo')) {
            $codigo         = $request->input('codigo');
            $propiedad      = Propiedad::where('codigo', $codigo)->with('tiposHabitacion')->with('tipoMonedas')->with('cuentasBancaria.tipoCuenta', 'tipoDepositoPropiedad.tipoDeposito')->with('politicas')->first();
            if (is_null($propiedad)) {
                $retorno  = array(
                    'msj'    => "Propiedad no encontrada",
                    'errors' => true);
                return Response::json($retorno, 404);
            }
        } else {
            $retorno = array(
                'msj'    => "No se envia codigo",
                'errors' => true);
            return Response::json($retorno, 400);
        }

        $propiedad_id              = $propiedad->id;
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

                $disponible_venta = $tipo->disponible_venta;
                $cantidad_disponibles = 0;
                foreach ($habitaciones_disponibles as $habitacion) {
                    if ($habitacion->tipo_habitacion_id == $tipo->id) {
                        $cantidad_disponibles += 1;
                    }
                }

                if ($disponible_venta <= $cantidad_disponibles) {
                    $disponibles = $disponible_venta;
                } elseif($disponible_venta > $cantidad_disponibles) {
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
                if ($tipo->disponible_venta > 0) {
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

    public function habitacionesDisponibles(Request $request)
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

        if ($request->has('reserva_id')) {
            $reserva_id = $request->input('reserva_id');
            $reserva = Reserva::where('id', $reserva_id)->first();
            if (is_null($reserva)) {
                $retorno = array(
                    'msj'    => "Reserva no encontrada",
                    'errors' => true);
                return Response::json($retorno, 404);
            }
        } else {
            $retorno = array(
                'msj'    => "No se envia reserva_id",
                'errors' => true);
            return Response::json($retorno, 400);
        }

        $tipo_hab_id  = $reserva->tipo_habitacion_id;
        $inicio       = $reserva->checkin;
        $fin          = $reserva->checkout;
        $fecha_inicio = $inicio->startOfDay()->format('Y-m-d');
        $fecha_fin    = $fin->startOfDay()->format('Y-m-d');

        $habitaciones_disponibles = Habitacion::where('propiedad_id', $request->input('propiedad_id'))
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
        ->where('tipo_habitacion_id', $tipo_hab_id)
        ->with('tipoHabitacion')
        ->get();

        return $habitaciones_disponibles;

    }

    public function getReservasMotor(Request $request)
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

        $clientes = Cliente::where(function ($query) use ($propiedad_id) {
            $query->whereHas('reservas.tipoHabitacion', function($query) use($propiedad_id){
                $query->where('propiedad_id', $propiedad_id);
            });
            $query->whereHas('reservas', function($query){
                $query->where('tipo_fuente_id', 1)->where('habitacion_id', null);
            });
        })
        ->with(['reservas' => function ($query) use ($propiedad_id){
                $query->whereHas('tipoHabitacion', function($query) use($propiedad_id){
                        $query->where('propiedad_id', $propiedad_id);
                    })
                    ->where('habitacion_id', null)
                    ->where('tipo_fuente_id', 1)
                    ->whereIn('estado_reserva_id', [1,2,3,4,5])
                    ->orderby('n_reserva_motor')
                    ->with('TipoMoneda')
                    ->with('tipoHabitacion');
            }])
        ->get();


        $data = []; //Arreglo principal
        $aux  = 0; //aux de n_reserva_motor

        foreach ($clientes as $cliente) {
            $suma_deposito = 0;
            $total         = 0;
            $aux_reservas  = []; //Arreglo aux de reserva del mismo cliente y misma operacion desde el motor

                $reservas = $cliente['reservas'];
                $cantidad = count($reservas) - 1;

                foreach ($reservas as $reserva) {

                if ($aux != $reserva->n_reserva_motor) {
                    $aux = $reserva->n_reserva_motor; //Lo igualo por si existe otra reserva con el mismo n_reserva_motor
                    if (count($aux_reservas) != 0) {
                        $aux_cliente['id']          = $cliente->id;
                        $aux_cliente['nombre']      = $cliente->nombre;
                        $aux_cliente['apellido']    = $cliente->apellido;
                        $aux_cliente['rut']         = $cliente->rut;
                        $aux_cliente['direccion']   = $cliente->direccion;
                        $aux_cliente['ciudad']      = $cliente->ciudad;
                        $aux_cliente['telefono']    = $cliente->telefono;
                        $aux_cliente['email']       = $cliente->email;
                        $aux_cliente['giro']        = $cliente->giro;
                        $aux_cliente['pais']        = $cliente->pais;
                        $aux_cliente['region']      = $cliente->region;
                        $aux_cliente['tipo_cliente']      = $cliente->tipoCliente;
                        $aux_cliente['suma_deposito']     = $suma_deposito;
                        $aux_cliente['monto_total']       = $total;
                        $aux_cliente['nombre_moneda']     = $reserva->tipoMoneda->nombre;
                        $aux_cliente['cantidad_decimales']      = $reserva->tipoMoneda->cantidad_decimales;
                        $aux_cliente['tipo_moneda_id']          = $reserva->tipo_moneda_id;
                        $aux_cliente['habitaciones_reservadas'] = count($aux_reservas);
                        $aux_cliente['reservas']                = $aux_reservas;

                        array_push($data, $aux_cliente);
                        $aux_reservas  = [];
                        $suma_deposito = 0;
                        $total         = 0;
                        array_push($aux_reservas, $reserva);
                        $suma_deposito += $reserva->monto_deposito;
                        $total         += $reserva->monto_total;

                        if ($reservas[$cantidad] == $reserva) {
                            $aux_cliente['id']          = $cliente->id;
                            $aux_cliente['nombre']      = $cliente->nombre;
                            $aux_cliente['apellido']    = $cliente->apellido;
                            $aux_cliente['rut']         = $cliente->rut;
                            $aux_cliente['direccion']   = $cliente->direccion;
                            $aux_cliente['ciudad']      = $cliente->ciudad;
                            $aux_cliente['telefono']    = $cliente->telefono;
                            $aux_cliente['email']       = $cliente->email;
                            $aux_cliente['giro']        = $cliente->giro;
                            $aux_cliente['pais']        = $cliente->pais;
                            $aux_cliente['region']      = $cliente->region;
                            $aux_cliente['tipo_cliente']      = $cliente->tipoCliente;
                            $aux_cliente['suma_deposito']     = $suma_deposito;
                            $aux_cliente['nombre_moneda']     = $reserva->tipoMoneda->nombre;
                            $aux_cliente['cantidad_decimales'] = $reserva->tipoMoneda->cantidad_decimales;
                            $aux_cliente['tipo_moneda_id']     = $reserva->tipo_moneda_id;
                            $aux_cliente['suma_deposito']      = $suma_deposito;
                            $aux_cliente['monto_total']        = $total;
                            $aux_cliente['habitaciones_reservadas'] = count($aux_reservas);
                            $aux_cliente['reservas']                = $aux_reservas;

                            array_push($data, $aux_cliente);
                            $aux_reservas = [];

                        }
                    } elseif (count($aux_reservas) == 0) {

                        if ($reservas[$cantidad] == $reserva) {
                            $suma_deposito = 0;
                            $total         = 0;
                            $suma_deposito += $reserva->monto_deposito;
                            $total         += $reserva->monto_total;
                            array_push($aux_reservas, $reserva);
                            $aux_cliente['id']          = $cliente->id;
                            $aux_cliente['nombre']      = $cliente->nombre;
                            $aux_cliente['apellido']    = $cliente->apellido;
                            $aux_cliente['rut']         = $cliente->rut;
                            $aux_cliente['direccion']   = $cliente->direccion;
                            $aux_cliente['ciudad']      = $cliente->ciudad;
                            $aux_cliente['telefono']    = $cliente->telefono;
                            $aux_cliente['email']       = $cliente->email;
                            $aux_cliente['giro']        = $cliente->giro;
                            $aux_cliente['pais']        = $cliente->pais;
                            $aux_cliente['region']      = $cliente->region;
                            $aux_cliente['tipo_cliente']            = $cliente->tipoCliente;
                            $aux_cliente['suma_deposito']           = $suma_deposito;
                            $aux_cliente['nombre_moneda']           = $reserva->tipoMoneda->nombre;
                            $aux_cliente['cantidad_decimales']      = $reserva->tipoMoneda->cantidad_decimales;
                            $aux_cliente['tipo_moneda_id']          = $reserva->tipo_moneda_id;
                            $aux_cliente['suma_deposito']           = $suma_deposito;
                            $aux_cliente['monto_total']             = $total;
                            $aux_cliente['habitaciones_reservadas'] = count($aux_reservas);
                            $aux_cliente['reservas']                = $aux_reservas;

                            array_push($data, $aux_cliente);
                            $suma_deposito = 0;
                            $total         = 0;
                            $aux_reservas = [];
                        } else {

                            $suma_deposito += $reserva->monto_deposito;
                            $total         += $reserva->monto_total;
                            array_push($aux_reservas, $reserva);
                        }
                    } 

                } elseif($aux == $reserva->n_reserva_motor) {

                    if ($reservas[$cantidad] == $reserva) {
                        $suma_deposito += $reserva->monto_deposito;
                        $total         += $reserva->monto_total;
                        array_push($aux_reservas, $reserva);
                        $aux_cliente['id']          = $cliente->id;
                        $aux_cliente['nombre']      = $cliente->nombre;
                        $aux_cliente['apellido']    = $cliente->apellido;
                        $aux_cliente['rut']         = $cliente->rut;
                        $aux_cliente['direccion']   = $cliente->direccion;
                        $aux_cliente['ciudad']      = $cliente->ciudad;
                        $aux_cliente['telefono']    = $cliente->telefono;
                        $aux_cliente['email']       = $cliente->email;
                        $aux_cliente['giro']        = $cliente->giro;
                        $aux_cliente['pais']        = $cliente->pais;
                        $aux_cliente['region']      = $cliente->region;
                        $aux_cliente['tipo_cliente']        = $cliente->tipoCliente;
                        $aux_cliente['suma_deposito']       = $suma_deposito;
                        $aux_cliente['nombre_moneda']       = $reserva->tipoMoneda->nombre;
                        $aux_cliente['cantidad_decimales']  = $reserva->tipoMoneda->cantidad_decimales;
                        $aux_cliente['tipo_moneda_id']      = $reserva->tipo_moneda_id;
                        $aux_cliente['suma_deposito']       = $suma_deposito;
                        $aux_cliente['monto_total']         = $total;
                        $aux_cliente['habitaciones_reservadas'] = count($aux_reservas);
                        $aux_cliente['reservas']                = $aux_reservas;

                        array_push($data, $aux_cliente);
                        $aux_reservas  = [];
                        $suma_deposito = 0;
                        $total         = 0;
                    } else {
                        $suma_deposito += $reserva->monto_deposito;
                        $total         += $reserva->monto_total;
                        array_push($aux_reservas, $reserva);
                    }
                }
            }
        }

        $reservas_mapa = $this->getReservasMapa(
            $propiedad_id
        );
        // junta reservas motor y mapa

        $resultado   = array_merge($data, $reservas_mapa);
        $aux         = 0;
        $aux_cliente = null;
        $i = 0;
        $reservas_final = [];
        while (count($resultado) != 0) {
            foreach ($resultado as $cliente) {
                if ($aux == 0) {
                    $aux = $cliente['reservas'][0]['created_at'];
                    $aux_cliente = $cliente;

                } else {
                    if ($aux < $cliente['reservas'][0]['created_at']) {
                        $aux = $cliente['reservas'][0]['created_at'];
                        $aux_cliente = $cliente;
                    } 
                }

                $i++;
                if ($i == count($resultado)) {
                    $posicion = array_search($aux_cliente, $resultado);
                    unset($resultado[$posicion]);

                    array_push($reservas_final, $aux_cliente);
                    $aux = 0;
                    $i = 0;
                }
            }
        }

        return $reservas_final;              

    }

    public function getReservasCliente(Request $request)
    {
        if ($request->has('cliente_id') && $request->has('reservas') ){
            $cliente_id = $request->cliente_id;
            $reservas   = $request->reservas;
        } else {
            $retorno = array(
                'msj'    => "Incompleto",
                'errors' => true);
            return Response::json($retorno, 400);
        }

        $cliente = Cliente::where('id', $cliente_id)
        ->with('tipoCliente')
        ->with('pais', 'region')
        ->with(['reservas' => function ($query) use ($reservas) {
                $query->whereIn('id', $reservas)
                    ->whereIn('estado_reserva_id', [1,2,3,4,5])
                    ->with('TipoMoneda')
                    ->with('tipoHabitacion');
            }])
        ->first();

        $suma_deposito = 0;
        $total         = 0;
        $cantidad      = count($reservas) - 1;
        $i = 0;
        foreach ($cliente['reservas'] as $reserva) {
            $suma_deposito += $reserva->monto_deposito;
            $total         += $reserva->monto_total;
            $i++;
        }

        $cliente->suma_deposito           = $suma_deposito;
        $cliente->monto_total             = $total;
        $cliente->nombre_moneda           = $cliente['reservas'][0]->tipoMoneda->nombre;       
        $cliente->cantidad_decimales      = $cliente['reservas'][0]->tipoMoneda->cantidad_decimales; 
        $cliente->tipo_moneda_id          = $cliente['reservas'][0]->tipoMoneda->id;  
        $cliente->habitaciones_reservadas = $i;       

        return $cliente;
    }


    public function reserva(Request $request) {
        $propiedad_id = null;
        if ($request->has('codigo')) {
            $codigo = $request->input('codigo');
            $propiedad = Propiedad::where(
                'codigo', 
                $codigo
            )->first();
            
            $propiedad_id = $propiedad->id;
            
            if (is_null($propiedad)) {
                $retorno['msj'] = "No se envia propiedad_id";
                $retorno['errors'] = true;
                return Response::json($retorno, 404);
            }
        } else {
            return Response::json($retorno, 400);
        }

        if ($request->has('tipo_moneda_id') && $request->has('fecha_inicio') && $request->has('fecha_fin') && $request->has('iva') && $request->has('noches') && $request->has('habitaciones') && $request->has('cliente')) {
            $tipo_moneda_id = $request->tipo_moneda_id;
            $fecha_inicio   = $request->fecha_inicio;
            $fecha_fin      = $request->fecha_fin;
            $iva            = $request->iva;
            $noches         = $request->noches;
            $clientes       = $request->cliente;
            $habitaciones   = $request->habitaciones;

            if (!is_array($habitaciones)) {
                $habitaciones = [];
                $habitaciones . push($request->habitaciones);
            }

            $reservas = Reserva::whereHas(
                'tipoHabitacion', 
                function($query) use($propiedad_id) {
                    $query->where(
                        'propiedad_id', 
                        $propiedad_id
                    );
                }
            )
            // ->where('habitacion_id', null)
            ->where('tipo_fuente_id', 1)
            ->whereIn('estado_reserva_id', [1,2,3,4,5])
            ->orderby('n_reserva_motor', 'DESC')
            ->get();

            $reserva = $reservas->first();
            
            if (!is_null($reserva)) {
                $n_reserva_motor = $reserva->n_reserva_motor;
            } else {
                $n_reserva_motor = 0;
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
                $reserva->tipo_fuente_id        = 1;
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
                $reserva->n_reserva_motor       = $n_reserva_motor + 1;
                $reserva->save();
            }

            if ($propiedad_id != null) {
                Event::fire(
                    new ReservasMotorEvent($propiedad_id)
                );

                $arr = array(
                    'propiedad'     => $propiedad
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
            }
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

    public function prueba(Request $request) {
        $propiedad_id = $request->prop_id;
        $reservas = Reserva::whereHas(
                    'tipoHabitacion',
                    function($query) use ($propiedad_id) {
                        $query->where('propiedad_id', $propiedad_id);
                    }
                )->orderby('id','DESC')
                ->where('n_reserva_motor', $request->n_reserva_motor);
        return Response::json($reservas);
    }

    public function asignarHabitacion(Request $request) {
        if ($request->has('reserva_id')) {
            $reserva_id = $request->input('reserva_id');
            $reserva = Reserva::where('id', $reserva_id)->first();
            if (is_null($reserva)) {
                $retorno = array(
                    'msj'    => "Reserva no encontrada",
                    'errors' => true);
                return Response::json($retorno, 404);
            }
        } else {
            $retorno = array(
                'msj'    => "No se envia reserva_id",
                'errors' => true
            );
            return Response::json($retorno, 400);
        }

        if ($request->has('habitacion_id')) {
            $habitacion_id = $request->input('habitacion_id');
            $habitacion = Habitacion::where('id', $habitacion_id)->first();
            if (is_null($habitacion)) {
                $retorno = array(
                    'msj'    => "Habitacion no encontrada",
                    'errors' => true);
                return Response::json($retorno, 404);
            }
        } else {
            $retorno = array(
                'msj'    => "No se envia habitacion_id",
                'errors' => true);
            return Response::json($retorno, 400);
        }

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

        $reservas = Reserva::whereHas(
            'habitacion', 
            function($query) use ($propiedad_id) {
                $query->where('propiedad_id', $propiedad_id);
            }
        )->orderby('id','DESC')
            ->where('numero_reserva', '!=', null)
            ->take(1)
        ->first();

        if (!is_null($reservas)) {
            $numero = $reservas->numero_reserva + 1;
        } else {
            $numero = 1;    
        }

        $reserva->update(array('numero_reserva' => $numero , 'habitacion_id' => $habitacion_id));

        if ($request->has('terminado')) {

            $arr = array(
                'propiedad'     => $propiedad,
                'cliente'       => $reserva->cliente,
                'reserva'       => $reserva
            ); 

            $this->EnvioCorreo(
                $propiedad,
                $reserva->cliente->email,
                $arr,
                "correos.comprobante_reserva_motor",
                "", // correo de la propiedad
                "",
                1,
                "",
                "reservas-varias"
            );

            $retorno['errors'] = false;
            $retorno['msj'] = 'Habitacion asignada, y comprobante enviado';
            return Response::json($retorno, 200);
        }

        $retorno = [
            'errors' => false,
            'msj'    => 'Habitacion asignada',];
        return Response::json($retorno, 201);
    }

    public function asignarColorMotor(Request $request)
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

        if ($request->has('colores')) {
            $colores = $request->input('colores');

        } else {
            $retorno = array(
                'msj'    => "No se envia colores",
                'errors' => true);
            return Response::json($retorno, 400);
        }

        foreach ($colores as $color) {
            $color_motor          = $color['color_motor_id'];
            $clasificacion_color  = $color['clasificacion_color_id'];

            $propiedad->clasificacionColores()->attach($clasificacion_color, ['color_motor_id' => $color_motor]);

        }

        $retorno = array(
            'msj'   => "colores ingresados correctamente",
            'erros' => false,);
        return Response::json($retorno, 201);

    }

    public function editarColor(Request $request)
    {
        if ($request->has('colores')) {
            $colores = $request->get('colores');
            foreach ($colores as $color) {
                $color_motor_id         = $color['color_motor_id'];
                $clasificacion_color_id = $color['clasificacion_color_id'];
                $color = MotorPropiedad::where('id', $color['color_id'])->first();
                if (!is_null($color)) {
                    $color->update(array('color_motor_id' => $color_motor_id, 'clasificacion_color_id' => $clasificacion_color_id));

                } else {
                    $retorno = array(
                        'msj'    => "Color no encontrada",
                        'errors' => true);
                    return Response::json($retorno, 404);
                }
            }
            $retorno = [
                'errors' => false,
                'msj'    => 'Color actualizada satisfactoriamente',];
            return Response::json($retorno, 201);
        } else {
            $retorno = array(
                'msj'    => "No se envia colores",
                'errors' => true);
            return Response::json($retorno, 400);
        }

    }

    /**
     * Obtiene coloresde la propiedad para motor de reserva
     *
     * @author ALLEN
     *
     * @param  Request          $request (codigo)
     * @return Response::json
     */
    public function getColoresPropiedad(Request $request)
    {
        if ($request->has('codigo')) {
            $codigo     = $request->input('codigo');
            $propiedad  = Propiedad::where('codigo', $codigo)->with('coloresMotor')->first();
            if (is_null($propiedad)) {
                $retorno = array(
                    'msj'    => "Propiedad no encontrada",
                    'errors' => true);
                return Response::json($retorno, 404);
            }
        } else {
            $retorno = array(
                'msj'    => "No se envia codigo",
                'errors' => true);
            return Response::json($retorno, 400);
        }

        return $propiedad;
    }

    public function getColores()
    {
        $colores = ColorMotor::all();
        return $colores;

    }

    public function getClasificacionColores()
    {
        $clasificacion = ClasificacionColor::all();
        return $clasificacion;
    }
}