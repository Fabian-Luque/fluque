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
use App\Temporada;
use App\TipoComprobante;
use App\Caja;
use App\HuespedReservaServicio;
use Illuminate\Http\Request;
use Response;
use \Carbon\Carbon;
use Validator;



class ReservaController extends Controller
{
    /** Busqueda avanzada de reservas, vista reservas
     * 
     *
     * @author ALLEN
     *
     * @param  Request          $request ()
     * @return Response::json
     */
    public function filtroReservas(Request $request, Reserva $reserva)
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

        $reservas = $reserva->select('reservas.id', 'numero_reserva' ,'checkin', 'habitacion_id', 'estado_reserva_id' ,'checkout', 'monto_total','estado_reserva.nombre as estado', 'ocupacion','cliente_id', 'clientes.nombre as nombre_cliente', 'clientes.apellido as apellido_cliente', 'noches', 'tipo_moneda.nombre as nombre_moneda', 'cantidad_decimales')
        ->whereHas('habitacion', function($query) use($propiedad_id){
        $query->where('propiedad_id', $propiedad_id);})
        ->with(['huespedes' => function ($q){
        $q->select('huespedes.id', 'nombre', 'apellido');}])
        ->with('habitacion.tipoHabitacion')
        ->join('clientes', 'clientes.id','=','cliente_id')
        ->join('tipo_moneda', 'tipo_moneda.id', '=', 'tipo_moneda_id')
        ->join('estado_reserva', 'estado_reserva.id', '=', 'estado_reserva_id')
        ->get();

        return $data = ['reservas' => $reservas];

    }

    /**
     * cambiar checkin y checkout de una reserva
     *
     * @author ALLEN
     *
     * @param  Request          $request ($reserva_id, $fecha_inicio, $fecha_fin)
     * @return Response::json
     */
    public function cambiarFechasReserva(Request $request)
    {
        if ($request->has('reserva_id')) {
            $reserva_id = $request->input('reserva_id');
            $reserva    = Reserva::where('id', $reserva_id)->first();
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

        if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
            $hab_id         = $reserva->habitacion_id;
            $fecha_inicio   = $request->input('fecha_inicio');
            $fecha_fin      = $request->input('fecha_fin');

            $habitacion_disponible = Habitacion::where('id', $hab_id)
            ->whereDoesntHave('reservas', function ($query) use ($fecha_inicio, $fecha_fin, $reserva_id) {
                $query->whereIn('estado_reserva_id', [1,2,3,4,5])->where('id', '!=', $reserva_id)
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
            ->first();

            if (!is_null($habitacion_disponible)) {
                $reserva->update(array('checkin' => $fecha_inicio, 'checkout' => $fecha_fin));
            } else {
                $retorno = array(
                  'msj'    => "La habitación no se encuentra disponible entre las fechas seleccionadas",
                  'errors' => true);
                return Response::json($retorno, 400);
            }
            $data = array(
                'errors' => false,
                'msg'    => 'Reserva actualizada satisfactoriamente',);
            return Response::json($data, 201);
        } else {
          $retorno = array(
              'msj'    => "No se envian fechas",
              'errors' => true);
          return Response::json($retorno, 400);
        }

    }

    /**
     * habitaciones disponibles para cambiar de habitacion una reserva
     *
     * @author ALLEN
     *
     * @param  Request          $request ($reserva_id, $propiedad_id)
     * @return Response::json
     */
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

        $inicio       = $reserva->checkin;
        $fin          = $reserva->checkout;
        $fecha_inicio = $inicio->startOfDay()->format('Y-m-d');
        $fecha_fin    = $fin->startOfDay()->format('Y-m-d');

        $habitaciones_disponibles = TipoHabitacion::where('propiedad_id', $propiedad_id)
        ->with(['habitaciones' => function ($query) use($fecha_inicio, $fecha_fin){
            $query->whereDoesntHave('reservas', function ($query) use ($fecha_inicio, $fecha_fin) {
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
            });
        }])
        ->get();

        return $habitaciones_disponibles;

    }


    /**
     * obtener reservas pendientes de pago
     *
     * @author ALLEN
     *
     * @param  Request          $request ($propiedad_id)
     * @return Response::json
     */
    public function getCuentasCredito(Request $request)
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

        $reservas = Reserva::select('reservas.id', 'numero_reserva', 'checkin', 'checkout', 'clientes.nombre as nombre_cliente', 'clientes.apellido as apellido_cliente', 'clientes.rut', 'tipo_moneda.nombre as nombre_moneda', 'cantidad_decimales' )
        ->whereHas('habitacion',function($query) use($propiedad_id){
            $query->where('propiedad_id', $propiedad_id);
        })
        ->where('estado_reserva_id', 5)
        ->with(['pagos' => function ($query){
            $query->where('metodo_pago_id', 2)->with('metodoPago', 'tipoComprobante', 'tipoMoneda');
        }])
        ->join('clientes', 'clientes.id','=','cliente_id')
        ->join('tipo_moneda', 'tipo_moneda.id', '=', 'tipo_moneda_id')
        ->get();

        $facturadas    = [];
        $no_facturadas = [];
        foreach ($reservas as $reserva) {
            $suma_pago = 0;
            foreach ($reserva['pagos'] as $pago) {
                $suma_pago += $pago->monto_equivalente;
                if ($pago->tipo == "Pago habitacion") {
                    if ($pago->tipo_comprobante_id == null) {
                        array_push($no_facturadas, $reserva);
                    } else {
                        array_push($facturadas, $reserva);
                    }
                }
            }
            $reserva->pago_total = $suma_pago;
        }

        $data['facturadas']    = $facturadas;
        $data['no_facturadas'] = $no_facturadas;
        return $data;

    }

    public function confirmarPagoReserva(Request $request)
    {
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

        $fecha_actual = Carbon::now();
        $pagos        = Pago::where('reserva_id', $reserva_id)->where('metodo_pago_id', 2)->get();
        $reserva->update(array('estado_reserva_id' => 4));

        foreach ($pagos as $pago) {
            $pago->update(array('estado' => 1, 'created_at' => $fecha_actual));
        }

        $retorno = array(
            'msj'       => "Pago confirmado satisfactoriamente",
            'errors'    => false);
        return Response::json($retorno, 201);

    }



    public function editarPago(Request $request, $id)
    {
        $rules = array(
            'numero_cheque'            => '',
            'numero_operacion'         => '',
            'tipo_comprobante_id'      => '',
            'metodo_pago_id'           => '',
            'fecha'                    => 'date',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $data = [
                'errors' => true,
                'msg'    => $validator->messages(),];
            return Response::json($data, 400);
        } else {
            if ($request->has('fecha')) {
                $getFecha = new Carbon($request->input('fecha'));
            }

            $pago       = Pago::where('id', $id)->first();
            $reserva_id = $pago->reserva_id;
            $reserva    = Reserva::where('id', $reserva_id)->first();

            if ($pago->metodo_pago_id == 2) {
                $pagos = Pago::where('reserva_id', $reserva_id)->where('metodo_pago_id', 2)->get();

                foreach ($pagos as $pago) {
                    $pago                       = Pago::findOrFail($pago->id);
                    $pago->numero_operacion     = $request->input('numero_operacion');
                    $pago->tipo_comprobante_id  = $request->input('tipo_comprobante_id');
                    $pago->numero_cheque        = $request->input('numero_cheque');
                    $pago->metodo_pago_id       = $request->input('metodo_pago_id');
                    if ($pago->estado == 0 && $request->input('metodo_pago_id') != 2) {
                        $pago->estado               = 1;
                    }
                    $pago->created_at           = $getFecha;
                    $pago->touch();
                }
            } else {
                $pago                       = Pago::findOrFail($pago->id);
                $pago->numero_operacion     = $request->input('numero_operacion');
                $pago->tipo_comprobante_id  = $request->input('tipo_comprobante_id');
                $pago->numero_cheque        = $request->input('numero_cheque');
                $pago->metodo_pago_id       = $request->input('metodo_pago_id');
                if ($pago->estado == 0 && $request->input('metodo_pago_id') != 2) {
                    $pago->estado               = 1;
                }
                $pago->created_at           = $getFecha;
                $pago->touch();
            }

            $pago = Pago::where('reserva_id', $reserva_id)->where('metodo_pago_id', 2)->first();
            if (is_null($pago)) {
                if ($reserva->estado_reserva_id == 5 && $reserva->monto_por_pagar == 0) {
                    $reserva->update(array('estado_reserva_id' => 4));
                }
            }
            
            $data = [
                'errors' => false,
                'msg'    => 'Pago actualizado satisfactoriamente',];
            return Response::json($data, 201);

        }
    }


    public function eliminarPago($id)
    {
        $pago = Pago::findOrFail($id);
        $reserva_id = $pago->reserva_id;
        $reserva = Reserva::where('id', $reserva_id)->first();
        if ($pago->tipo == 'Pago habitacion' || $pago->tipo == "Confirmacion de reserva") {
            $monto_por_pagar = $reserva->monto_por_pagar + $pago->monto_pago;
            $reserva->update(array('monto_por_pagar' => $monto_por_pagar));
            if ($reserva->monto_total == $reserva->monto_por_pagar && $reserva->estado_reserva_id ==2) {
                $reserva->update(array('estado_reserva_id' => 1));
            }
        }
        if ($pago->tipo == 'Pago consumos') {
            $servicios = $pago->servicios;
            if (count($servicios) == 0) {
                $retorno = array(
                    'errors' => true,
                    'msj'    => " No autorizado",
                );
                return Response::json($retorno, 400);
            }

            $monto_por_pagar = $reserva->monto_por_pagar + $pago->monto_pago;
            $reserva->update(array('monto_por_pagar' => $monto_por_pagar));
            foreach ($servicios as $servicio) {
            $servicio->update(array('estado' => 'Por pagar'));
            }
        }
        $pago->delete();

        if ($reserva->estado_reserva_id == 4) {
           $reserva->update(array('estado_reserva_id' => 5));
        }

        $retorno = [
            'errors' => false,
            'msj'    => 'Pago eliminado satisfactoriamente',
        ];

        return Response::json($retorno, 202);

    }


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
        if ($request->has('tipo_moneda_id') && $request->has('fecha_inicio') && $request->has('fecha_fin') && $request->has('iva') && $request->has('noches') && $request->has('habitacion_info') && $request->has('cliente')) {
            $tipo_moneda_id      = $request->get('tipo_moneda_id');
            $fecha_inicio        = $request->get('fecha_inicio');
            $fecha_fin           = $request->get('fecha_fin');
            $iva                 = $request->get('iva');
            $noches              = $request->get('noches');
            $clientes            = $request['cliente'];
            $habitaciones_info   = $request['habitacion_info'];

            if (!is_array($habitaciones_info)) {
                $habitaciones_info = [];
                $habitaciones_info . push($request['habitacion_info']);
            }

            $fecha_inicio   = $request->input('fecha_inicio');
            $fecha_fin      = $request->input('fecha_fin');
            $tipo_moneda_id = $request->input('tipo_moneda_id');

            if ($clientes['tipo_cliente_id'] == 1) {
                if ($request->has('cliente.rut')) {
                    $cliente                       = Cliente::firstOrNew($request['cliente']);
                    $cliente->tipo_cliente_id      = $clientes['tipo_cliente_id'];
                    $cliente->nombre               = $clientes['nombre'];
                    $cliente->apellido             = $clientes['apellido'];
                    $cliente->giro                 = null;
                    $cliente->save();
                } else { 
                    $cliente                         = new Cliente();
                    $cliente->nombre                 = $clientes['nombre'];
                    $cliente->apellido               = $clientes['apellido'];
                    if($request->has('cliente.direccion')){
                    $cliente->direccion              = $clientes['direccion'];
                    }else{
                    $cliente->direccion              = null;
                    }
                    if($request->has('cliente.ciudad')){
                    $cliente->ciudad                 = $clientes['ciudad'];
                    }else{
                    $cliente->ciudad                 = null;
                    }
                    if($request->has('cliente.telefono')){
                    $cliente->telefono               = $clientes['telefono'];
                    }else{
                    $cliente->telefono               = null;
                    }
                    if($request->has('cliente.email')){
                    $cliente->email                  = $clientes['email'];
                    }else{
                    $cliente->email                  = null;
                    }
                    if($request->has('cliente.pais_id')) {
                    $cliente->pais_id                = $clientes['pais_id'];
                    }else{
                    $cliente->pais_id                = null;
                    }
                    if($request->has('cliente.region_id')) {
                    $cliente->region_id              = $clientes['region_id'];
                    }else{
                    $cliente->region_id              = null;
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

            foreach ($habitaciones_info as $habitacion_info) {
                $habitacion_id  = $habitacion_info['id'];
                $huespedes      = $habitacion_info['huespedes'];
                $propiedad_id   = $habitacion_info['propiedad_id'];

                $reserva = Reserva::whereHas('habitacion', function($query) use($propiedad_id){
                    $query->where('propiedad_id', $propiedad_id);})
                ->orderby('numero_reserva', 'DESC')
                ->first();

                if (!empty($reserva)) {
                    $numero = $reserva->numero_reserva;
                } else {
                    $numero = 0;    
                }

                $reserv = Reserva::where('habitacion_id', $habitacion_info['id'])->where('checkin', $fecha_inicio)->where('checkout', $fecha_fin)->where('estado_reserva_id', '!=', 6)->where('estado_reserva_id', '!=', 7)->first();

                if (is_null($reserv)) {
                    $reserva                        = new Reserva();
                    if (!empty($reserva)) {
                        $reserva->numero_reserva    = $numero + 1;
                    } else {
                        $reserva->numero_reserva    = 1;
                    }
                    $reserva->monto_alojamiento     = $habitacion_info['monto_alojamiento'];
                    $reserva->monto_total           = $habitacion_info['monto_alojamiento'];
                    $reserva->monto_consumo         = 0;
                    $reserva->monto_por_pagar       = $habitacion_info['monto_alojamiento'];
                    $reserva->ocupacion             = $habitacion_info['ocupacion'];
                    $reserva->tipo_fuente_id        = $request['tipo_fuente_id'];
                    $reserva->habitacion_id         = $habitacion_info['id'];
                    $reserva->cliente_id            = $cliente->id;
                    $reserva->checkin               = $fecha_inicio;
                    $reserva->checkout              = $fecha_fin;
                    $reserva->tipo_moneda_id        = $tipo_moneda_id;
                    $reserva->iva                   = $request['iva'];
                    $reserva->descuento             = $habitacion_info['descuento'];
                    $reserva->estado_reserva_id     = $request['estado_reserva_id'];
                    $reserva->noches                = $request['noches'];
                    $reserva->observacion           = $request['observacion'];
                    $reserva->detalle               = $request['detalle'];
                    $reserva->save();

                    if (!empty($huespedes)) {
                       foreach ($huespedes as $huesped) {
                            $huesped               = Huesped::firstOrNew($huesped);
                            $huesped->apellido     = $huesped['apellido'];
                            $huesped->rut          = $huesped['rut'];
                            $huesped->telefono     = $huesped['telefono'];
                            $huesped->save();
                            $reserva->huespedes()->attach($huesped->id);
                        }
                    }

                } else {
                    $retorno = array(
                        'msj'       => "Error: La reserva ya fué creada",
                        'errors'    => true);
                    return Response::json($retorno, 400);
                }
            }
            $retorno = array(
                'msj'       => "Reserva creada satisfactoriamente",
                'errors'    => false);
            return Response::json($retorno, 201);
        } else {
            $retorno = array(
                'msj'    => "Incompleto",
                'errors' => true);
            return Response::json($retorno, 400);
        }

    }



    public function editarReserva(Request $request)
    {
        if ($request->has('reserva_id')) {
          $reserva_id = $request->input('reserva_id');
          $reserva    = Reserva::where('id', $reserva_id)->first();
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

        if ($request->has('propiedad_id')) {
            $propiedad_id   = $request->input('propiedad_id');
            $propiedad      = Propiedad::where('id', $propiedad_id)->first();
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

        if ($request->has('iva')) {
            $iva = $request->input('iva');
            $reserva->update(array('iva' => $iva));
        }

        if ($request->has('descuento')) {
            $descuento = $request->input('descuento');
            $reserva->update(array('descuento' => $descuento));
        }

        if ($request->has('monto_alojamiento') && $request->has('monto_total') && $request->has('monto_por_pagar')) {
            $monto_alojamiento = $request->input('monto_alojamiento');
            $monto_total       = $request->input('monto_total');
            $monto_por_pagar   = $request->input('monto_por_pagar');

            if ($reserva->estado_reserva_id == 4) {
                if ($monto_por_pagar > 0) {
                $reserva->update(array('estado_reserva_id' => 5,'monto_alojamiento' => $monto_alojamiento, 'monto_total' => $monto_total, 'monto_por_pagar' => $monto_por_pagar));
                }
            }elseif ($reserva->estado_reserva_id == 5) {
                if ($monto_por_pagar == 0) {

                    $reserva->update(array('estado_reserva_id' => 4, 'monto_alojamiento' => $monto_alojamiento, 'monto_total' => $monto_total, 'monto_por_pagar' => $monto_por_pagar));
                } else {

                    $reserva->update(array('monto_alojamiento' => $monto_alojamiento, 'monto_total' => $monto_total, 'monto_por_pagar' => $monto_por_pagar));

                }
            }elseif($reserva->estado_reserva_id != 4 && $reserva->estado_reserva_id != 5){

                $reserva->update(array('monto_alojamiento' => $monto_alojamiento, 'monto_total' => $monto_total, 'monto_por_pagar' => $monto_por_pagar));
            }


            $retorno = [
              'errors' => false,
              'msj'    => 'Reserva actualizada satisfactoriamente',
            ];
            return Response::json($retorno, 201);
        }
        
        $reserva_checkout = $reserva->checkout;
        $reserva_checkin  = $reserva->checkin;
        $habitacion_id    = $reserva->habitacion_id;
        $hab       = Habitacion::where('id' , $habitacion_id)->first();

        if ($request->has('estado_reserva_id')) {
          $estado_reserva = $request->input('estado_reserva_id');
          if ($estado_reserva == 6) {
            $observacion  = $request->input('observacion');
            $reserva->update(array('estado_reserva_id' => $estado_reserva, 'observacion' => $observacion));
          }
          $reserva->update(array('estado_reserva_id' => $estado_reserva ));

          $retorno = [
              'errors' => false,
              'msj'    => 'Reserva anulada satisfactoriamente',
          ];
          return Response::json($retorno, 201);
          }

        if($request->has('fecha_inicio')){
          $habitacion_ocupada = [];
          $fecha_inicio       = $request->input('fecha_inicio');

          if($reserva_checkout > $fecha_inicio){
            $fechaInicio=strtotime($fecha_inicio);
            $fechaFin=strtotime($reserva_checkin);

            for($i=$fechaInicio; $i<$fechaFin; $i+=86400){
              $fecha      = date("Y-m-d", $i);
              $habitacion = Habitacion::where('id', $habitacion_id)->whereHas('reservas', function($query) use($fecha){
                $query->where('checkin','<=' ,$fecha)->where('checkout', '>', $fecha)->where('estado_reserva_id', '!=', 6)->where('estado_reserva_id', '!=', 7);
              })->get();

              if(count($habitacion) != 0){
                if(!in_array($habitacion, $habitacion_ocupada)){
                  array_push($habitacion_ocupada, $habitacion);
                    
                 }
              }
            } //fin for

            if(count($habitacion_ocupada) == 0){
            $precios                    = $hab->tipoHabitacion->precios;
            $tipo_habitacion_id         = $hab->tipo_habitacion_id;
            $propiedad_monedas          = $propiedad->tipoMonedas; // monedas propiedad
            $capacidad                  = $hab->tipoHabitacion->capacidad;

            $precio_promedio_habitacion = [];
            $auxPrecio                  = [];
            $auxFecha                   = new Carbon($request->input('fecha_inicio'));
            $auxFin                     = new Carbon($reserva->checkout);
            $noches = $auxFin->diffInDays($auxFecha);

            while ($auxFecha < $auxFin) {

                $temporada = Temporada::where('propiedad_id', $propiedad_id)->whereHas('calendarios', function ($query) use ($auxFecha) {
                    $query->where('fecha', $auxFecha);})->first();

                if (!is_null($temporada)) {

                    $temporada_id      = $temporada->id;
                    $precios_temporada = $precios->where('temporada_id', $temporada_id)->where('tipo_habitacion_id', $hab->tipo_habitacion_id);
                    foreach ($precios_temporada as $precio) {
                        if ($precio->precio == 0) {
                            $data = array(
                                'msj'    => "debe configurar precios para este tipo de habitacion",
                                'errors' => true,
                            );
                            return Response::json($data, 400);
                        }
                    }

                    if ($propiedad->tipo_cobro_id != 3) {
                        foreach ($propiedad_monedas as $moneda) {
                            $tipo_moneda = $moneda->id;

                            foreach ($precios_temporada as $precio) {
                                if ($tipo_moneda == $precio->tipo_moneda_id) {

                                    $precio_tipo_habitacion = ['cantidad_huespedes' => $precio->cantidad_huespedes, 'precio' => $precio->precio, 'tipo_moneda_id' => $moneda->id, 'nombre_moneda' => $moneda->nombre, 'cantidad_decimales' => $moneda->cantidad_decimales];

                                    array_push($auxPrecio, $precio_tipo_habitacion);
                                }
                            }
                        }

                    } else {

                        foreach ($propiedad_monedas as $moneda) {

                            $tipo_moneda = $moneda->id;
                            foreach ($precios_temporada as $precio) {
                                if ($tipo_moneda == $precio->tipo_moneda_id) {

                                    $precio_tipo_habitacion = ['cantidad_huespedes' => $precio->cantidad_huespedes, 'precio' => $precio->precio, 'tipo_moneda_id' => $moneda->id, 'nombre_moneda' => $moneda->nombre, 'cantidad_decimales' => $moneda->cantidad_decimales];

                                    array_push($auxPrecio, $precio_tipo_habitacion);
                                }
                            }
                        }
                    }

                } else {

                    $data = array(
                        'msj'    => "Debe configurar una temporada para la fecha " . $auxFecha,
                        'errors' => true,
                    );
                    return Response::json($data, 400);
                }

                $auxFecha->addDay();

            }

            $precio_promedio_habitacion = [];
            if ($propiedad->tipo_cobro_id != 3) {

                foreach ($propiedad_monedas as $moneda) {
                    $moneda_id  = $moneda->id;
                    $sumaPrecio = 0;
                    foreach ($auxPrecio as $precio_habitacion) {
                        if ($precio_habitacion['tipo_moneda_id'] == $moneda_id && $precio_habitacion['cantidad_huespedes'] == 1) {
                            $sumaPrecio += $precio_habitacion['precio'];
                        }
                    }

                    $precio_promedio = ['cantidad_huespedes' => 1, 'precio' => $sumaPrecio, 'tipo_moneda_id' => $moneda->id, 'nombre_moneda' => $moneda->nombre, 'cantidad_decimales' => $moneda->cantidad_decimales];

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

                        $precio_promedio = ['cantidad_huespedes' => $i, 'precio' => $sumaPrecio, 'tipo_moneda_id' => $moneda->id, 'nombre_moneda' => $moneda->nombre, 'cantidad_decimales' => $moneda->cantidad_decimales];

                        array_push($precio_promedio_habitacion, $precio_promedio);

                    }
                }
            }

            $cantidad_huespedes = $reserva->ocupacion;

            if ($propiedad->tipo_cobro_id != 3) {
                
                foreach ($precio_promedio_habitacion as $precio) {
                    if ($precio['tipo_moneda_id'] == $reserva->tipo_moneda_id) {
                        $precio_reserva = $precio['precio'];
                        if ($propiedad->tipo_cobro_id == 1) {
                            $precio_reserva = $precio['precio'];
                        } elseif ($propiedad->tipo_cobro_id == 2) {
                            $precio_reserva = $precio['precio'] * $cantidad_huespedes;
                        }
                    }
                }

            } else {

                foreach ($precio_promedio_habitacion as $precio) {
                    if ($precio['tipo_moneda_id'] == $reserva->tipo_moneda_id && $precio['cantidad_huespedes'] == $cantidad_huespedes) {
                        $precio_reserva = $precio['precio'];
                    }
                }
            }

            $iva_propiedad = $propiedad->iva;
            $iva_reserva   = $reserva->iva;
            $descuento     = $reserva->descuento;

            if ($reserva->iva == 0) {

                $iva               = (($precio_reserva * $iva_propiedad) / 100);
                $monto_alojamiento = $precio_reserva - $iva;
                if ($descuento != 0) {
                    $monto_alojamiento -= $descuento;
                }
                $monto_total       = $monto_alojamiento + $reserva->monto_consumo;

            } else {

                $monto_alojamiento = $precio_reserva;
                if ($descuento != 0) {
                    $monto_alojamiento -= $descuento;
                }
               $monto_total  = $monto_alojamiento + $reserva->monto_consumo;   
            }


           $pagos_realizados  = $reserva->pagos;
           $monto_pagado      = 0;
           foreach($pagos_realizados as $pago){
             $monto_pagado += $pago->monto_pago;
           }

           $monto_por_pagar = $monto_total - $monto_pagado;
           $reserva->update(array('monto_alojamiento' => $monto_alojamiento , 'monto_total' => $monto_total , 'monto_por_pagar' => $monto_por_pagar , 'checkin' => $fecha_inicio, 'noches' => $noches));

            }else{
              $retorno = array(
                  'msj'    => "Seleccionar otra fecha de checkin, la habitacion ya esta reservada",
                  'errors' => true
              );
              return Response::json($retorno, 400);
            }

          } else {
            $retorno = array(
                'msj'    => "Las fechas no corresponden",
                'errors' => true
            );
            return Response::json($retorno, 400);
          }
        }

        if($request->has('fecha_fin')){    
          $fecha_fin          = $request->input('fecha_fin');
          $reserva_checkout   = $reserva->checkout;
          $reserva_checkin    = $reserva->checkin;
          $habitacion_ocupada = [];

          if($reserva_checkin < $fecha_fin){
            $fechaInicio =strtotime($reserva_checkout)+86400;
            $fechaFin    =strtotime($fecha_fin);

            for($i=$fechaInicio; $i<=$fechaFin; $i+=86400){
              $fecha      = date("Y-m-d", $i);
              $habitacion = Habitacion::where('id', $habitacion_id)->whereHas('reservas', function($query) use($fecha){
                $query->where('checkin','<' ,$fecha)->where('checkout', '>', $fecha)->where('estado_reserva_id', '!=', 6)->where('estado_reserva_id', '!=', 7);
              })->get();

              if(count($habitacion) != 0) {
                if(!in_array($habitacion, $habitacion_ocupada)){
                  array_push($habitacion_ocupada, $habitacion);
                }
              }
            }

            if(count($habitacion_ocupada) == 0){

            $precios                    = $hab->tipoHabitacion->precios;
            $tipo_habitacion_id         = $hab->tipo_habitacion_id;
            $propiedad_monedas          = $propiedad->tipoMonedas; // monedas propiedad
            $capacidad                  = $hab->tipoHabitacion->capacidad;

            $precio_promedio_habitacion = [];
            $auxPrecio                  = [];
            $auxFecha                   = new Carbon($reserva->checkin);
            $auxFin                     = new Carbon($request->input('fecha_fin'));
            $noches = $auxFin->diffInDays($auxFecha);

            while ($auxFecha < $auxFin) {

                $temporada = Temporada::where('propiedad_id', $propiedad_id)->whereHas('calendarios', function ($query) use ($auxFecha) {
                    $query->where('fecha', $auxFecha);})->first();

                if (!is_null($temporada)) {

                    $temporada_id      = $temporada->id;
                    $precios_temporada = $precios->where('temporada_id', $temporada_id)->where('tipo_habitacion_id', $hab->tipo_habitacion_id);
                    foreach ($precios_temporada as $precio) {
                        if ($precio->precio == 0) {
                            $data = array(
                                'msj'    => "debe configurar precios para este tipo de habitacion",
                                'errors' => true,
                            );
                            return Response::json($data, 400);
                        }
                    }

                    if ($propiedad->tipo_cobro_id != 3) {
                        foreach ($propiedad_monedas as $moneda) {
                            $tipo_moneda = $moneda->id;

                            foreach ($precios_temporada as $precio) {
                                if ($tipo_moneda == $precio->tipo_moneda_id) {

                                    $precio_tipo_habitacion = ['cantidad_huespedes' => $precio->cantidad_huespedes, 'precio' => $precio->precio, 'tipo_moneda_id' => $moneda->id, 'nombre_moneda' => $moneda->nombre, 'cantidad_decimales' => $moneda->cantidad_decimales];

                                    array_push($auxPrecio, $precio_tipo_habitacion);
                                }
                            }
                        }

                    } else {

                        foreach ($propiedad_monedas as $moneda) {

                            $tipo_moneda = $moneda->id;
                            foreach ($precios_temporada as $precio) {
                                if ($tipo_moneda == $precio->tipo_moneda_id) {

                                    $precio_tipo_habitacion = ['cantidad_huespedes' => $precio->cantidad_huespedes, 'precio' => $precio->precio, 'tipo_moneda_id' => $moneda->id, 'nombre_moneda' => $moneda->nombre, 'cantidad_decimales' => $moneda->cantidad_decimales];

                                    array_push($auxPrecio, $precio_tipo_habitacion);
                                }
                            }
                        }
                    }

                } else {

                    $data = array(
                        'msj'    => "Debe configurar una temporada para la fecha " . $auxFecha,
                        'errors' => true,
                    );
                    return Response::json($data, 400);
                }

                $auxFecha->addDay();

            }

            $precio_promedio_habitacion = [];
            if ($propiedad->tipo_cobro_id != 3) {

                foreach ($propiedad_monedas as $moneda) {
                    $moneda_id  = $moneda->id;
                    $sumaPrecio = 0;
                    foreach ($auxPrecio as $precio_habitacion) {
                        if ($precio_habitacion['tipo_moneda_id'] == $moneda_id && $precio_habitacion['cantidad_huespedes'] == 1) {
                            $sumaPrecio += $precio_habitacion['precio'];
                        }
                    }

                    $precio_promedio = ['cantidad_huespedes' => 1, 'precio' => $sumaPrecio, 'tipo_moneda_id' => $moneda->id, 'nombre_moneda' => $moneda->nombre, 'cantidad_decimales' => $moneda->cantidad_decimales];

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

                        $precio_promedio = ['cantidad_huespedes' => $i, 'precio' => $sumaPrecio, 'tipo_moneda_id' => $moneda->id, 'nombre_moneda' => $moneda->nombre, 'cantidad_decimales' => $moneda->cantidad_decimales];

                        array_push($precio_promedio_habitacion, $precio_promedio);

                    }
                }
            }

            $cantidad_huespedes = $reserva->ocupacion;

            if ($propiedad->tipo_cobro_id != 3) {
                
                foreach ($precio_promedio_habitacion as $precio) {
                    if ($precio['tipo_moneda_id']== $reserva->tipo_moneda_id) {

                        if ($propiedad->tipo_cobro_id == 1) {
                            $precio_reserva = $precio['precio'];
                        } elseif ($propiedad->tipo_cobro_id == 2) {
                            $precio_reserva = $precio['precio'] * $cantidad_huespedes;
                        }
                    }
                }

            } else {

                foreach ($precio_promedio_habitacion as $precio) {
                    if ($precio['tipo_moneda_id'] == $reserva->tipo_moneda_id && $precio['cantidad_huespedes'] == $cantidad_huespedes) {
                        $precio_reserva = $precio['precio'];
                    }
                }
            }
        
            $iva_propiedad = $propiedad->iva;
            $iva_reserva   = $reserva->iva;
            $descuento     = $reserva->descuento;

            if ($reserva->iva == 0) {

                $iva               = (($precio_reserva * $iva_propiedad) / 100);
                $monto_alojamiento = $precio_reserva - $iva;
                if ($descuento != 0) {
                    $monto_alojamiento -= $descuento;
                }
                $monto_total       = $monto_alojamiento + $reserva->monto_consumo;

            } else {

                $monto_alojamiento = $precio_reserva;
                if ($descuento != 0) {
                    $monto_alojamiento -= $descuento;
                }
               $monto_total  = $monto_alojamiento + $reserva->monto_consumo;   
            }


           $pagos_realizados  = $reserva->pagos;
           $monto_pagado      = 0;
           foreach($pagos_realizados as $pago){
             $monto_pagado += $pago->monto_pago;
           }

           $monto_por_pagar = $monto_total - $monto_pagado;
           $reserva->update(array('monto_alojamiento' => $monto_alojamiento , 'monto_total' => $monto_total , 'monto_por_pagar' => $monto_por_pagar , 'checkout' => $auxFin, 'noches' => $noches));



            }else{
              $retorno = array(
                  'msj'    => "Seleccionar otra fecha de checkout, la habitacion ya esta reservada",
                  'errors' => true
              );
              return Response::json($retorno, 400);
            }

          }else{
            $retorno = [
              'errors' => false,
              'msj'    => 'Las fechas no corresponden',
            ];
            return Response::json($retorno, 201);
          }
        }

        if($request->has('precio_habitacion')){
          $precio_habitacion = $request->input('precio_habitacion');
          $noches            = ((strtotime($reserva_checkout)-strtotime($reserva_checkin))/86400);
          $monto_alojamiento = $noches * $precio_habitacion;
          $monto_total       = $monto_alojamiento + $reserva->monto_consumo;
          $pagos_realizados  = $reserva->pagos;
          $monto_pagado      = 0;
            foreach($pagos_realizados as $pago){
              $monto_pagado += $pago->monto_pago;
            }

            $monto_por_pagar = $monto_total - $monto_pagado;
            $reserva->update(array('precio_habitacion' => $precio_habitacion ,'monto_alojamiento' => $monto_alojamiento , 'monto_total' => $monto_total , 'monto_por_pagar' => $monto_por_pagar));
          }


        if($request->has('ocupacion')){
          $ocupacion           = $request->input('ocupacion');
          $disponibilidad_base = $hab->tipoHabitacion->capacidad;
          if($ocupacion <= $disponibilidad_base){
            $reserva->update(array('ocupacion' => $ocupacion));
          }else{
            $retorno = array(
              'msj'    => "El valor supera la disponibilidad de la habitación",
              'errors' => true
            );
            return Response::json($retorno, 400);
          }
        }

        if($request->has('observacion')){
          $observacion = $request->input('observacion');
          $reserva->update(array('observacion' => $observacion));
        }

        if($request->has('detalle')){
          $detalle = $request->input('detalle');
          $reserva->update(array('detalle' => $detalle));
        }

        $retorno = [
          'errors' => false,
          'msj'    => 'Reserva actualizada satisfactoriamente',
        ];
        return Response::json($retorno, 201);

    }



    public function cambiarHabitacion(Request $request)
    {
        if ($request->has('reserva_id') && $request->has('habitacion_id')) {
            $reserva_id     = $request->input('reserva_id');
            $habitacion_id  = $request->input('habitacion_id');
            $reserva        = Reserva::where('id', $reserva_id)->first();

            if (!is_null($reserva)) {
                $habitacion   = Habitacion::where('id', $habitacion_id)->first();

                if (!is_null($habitacion)) {
                $inicio       = $reserva->checkin;
                $fin          = $reserva->checkout;
                $fecha_inicio = $inicio->startOfDay()->format('Y-m-d');
                $fecha_fin    = $fin->startOfDay()->format('Y-m-d');

                $habitacion_disponible = Habitacion::where('id', $habitacion_id)
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

                if (!is_null($habitacion_disponible)) {
                    $reserva->update(array('habitacion_id' => $habitacion_id));
                } else {
                    $retorno = array(
                      'msj'    => "La habitación no se encuentra disponible",
                      'errors' => true);
                    return Response::json($retorno, 400);
                }

                $retorno = [
                    'errors' => false,
                    'msj' => 'Reserva actualizada satisfactoriamente',];
                return Response::json($retorno, 201);

                } else {
                    $retorno = array(
                        'msj' => "Habitación no encontrada",
                        'errors' => true);
                    return Response::json($retorno, 404);
                }
            } else {
                $retorno = array(
                    'msj' => "Reserva no encontrada",
                    'errors' => true);
                return Response::json($retorno, 404);
            }
        } else {
            $retorno = array(
                'msj'    => "La solicitud está incompleta",
                'errors' => true);
            return Response::json($retorno, 400);
        }

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


    public function getReservas(Request $request)
    {
        if ($request->has('propiedad_id')) {
            $id        = $request->input('propiedad_id');
            $propiedad = Propiedad::where('id', $id)->first();
            if(is_null($propiedad)){
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
    
        $reservas = Reserva::select('reservas.id', 'numero_reserva' ,'checkin', 'habitacion_id', 'estado_reserva_id' ,'checkout','ocupacion', 'monto_total','estado_reserva.nombre as estado' ,'cliente_id', 'clientes.nombre as nombre_cliente', 'clientes.apellido as apellido_cliente', 'noches', 'tipo_moneda.nombre as nombre_moneda', 'cantidad_decimales', 'monto_por_pagar')
        ->whereHas('habitacion', function($query) use($id){
            $query->where('propiedad_id', $id);})
        ->with(['huespedes' => function ($q){
            $q->select('huespedes.id', 'nombre', 'apellido');}])
        ->with('habitacion.tipoHabitacion')
        ->with('cliente.pais', 'cliente.region')
        ->join('clientes', 'clientes.id','=','cliente_id')
        ->join('tipo_moneda', 'tipo_moneda.id', '=', 'tipo_moneda_id')
        ->join('estado_reserva', 'estado_reserva.id', '=', 'estado_reserva_id')
        ->orderBy('reservas.id', 'desc')
        ->take(50)
        ->get();

        $data = ['reservas' => $reservas,];

        return $data;      
    }


    public function pagoReserva(Request $request)
    {
        if ($request->has('pagos')) {
            $pagos = $request->get('pagos');
        } else {
            $retorno = array(
                'msj'    => "No se envia pagos",
                'errors' => true);
            return Response::json($retorno, 400);
        }

        if ($request->has('propiedad_id')) {
            $propiedad_id = $request->get('propiedad_id');
            $propiedad    = Propiedad::where('id', $propiedad_id)->first();
            if(is_null($propiedad)){
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

        $caja_abierta  = Caja::where('propiedad_id', $propiedad->id)->where('estado_caja_id', 1)->first();
        if (!is_null($caja_abierta)) {
            foreach ($request['pagos'] as $pago){
                $metodo_pago         = $pago['metodo_pago_id'];
                $monto_pago          = $pago['monto_pago'];
                $numero_operacion    = $pago['numero_operacion'];
                $tipo_comprobante_id = $pago['tipo_comprobante_id'];
                $tipo_pago           = $pago['tipo_pago'];
                $reserva_id          = $pago['reserva_id'];
                $tipo_moneda_id      = $pago['tipo_moneda_id'];
                $monto_equivalente   = $pago['monto_equivalente'];

                $reserva = Reserva::where('id', $reserva_id)->with('pagos')->first();


                if (!is_null($reserva)) {
                    
                    if (isset($pago['numero_cheque'])) {
                    $numero_cheque = $pago['numero_cheque'];
                    }
                    
                    if ($tipo_pago == "Confirmacion de reserva") {
                        if ($reserva->estado_reserva_id == 1) {
                            $monto = $reserva->monto_por_pagar;
                            $monto -= $monto_pago;

                            if (isset($pago['monto_pago']) && !is_null($monto_equivalente)) {
                                $pago                        = new Pago();
                                $pago->monto_pago            = $monto_pago;
                                $pago->monto_equivalente     = $monto_equivalente; 
                                $pago->tipo                  = $tipo_pago;
                                $pago->numero_operacion      = $numero_operacion;
                                $pago->tipo_comprobante_id   = $tipo_comprobante_id;
                                $pago->metodo_pago_id        = $metodo_pago;
                                $pago->tipo_moneda_id        = $tipo_moneda_id;
                                if ($metodo_pago == 4) {
                                $pago->numero_cheque         = $numero_cheque;
                                }
                                $pago->reserva_id            = $reserva->id;
                                $pago->caja_id               = $caja_abierta->id;
                                $pago->estado                = 1;
                                $pago->save();
                                
                            } else {
                                $pago                        = new Pago();
                                $pago->monto_pago            = $monto_pago;
                                $pago->monto_equivalente     = $monto_pago; 
                                $pago->tipo                  = $tipo_pago;
                                $pago->numero_operacion      = $numero_operacion;
                                $pago->tipo_comprobante_id   = $tipo_comprobante_id;
                                $pago->metodo_pago_id        = $metodo_pago;
                                $pago->tipo_moneda_id        = $tipo_moneda_id;
                                if($metodo_pago == 4){
                                $pago->numero_cheque         = $numero_cheque;
                                }
                                $pago->reserva_id            = $reserva->id;
                                $pago->caja_id               = $caja_abierta->id;
                                $pago->estado                = 1;
                                $pago->save();
                            }

                            $reserva->update(array('monto_por_pagar' => $monto , 'estado_reserva_id' => 2));

                            $data = array(
                                'msj' => "Deposito sugerido ingresado satisfactoriamente",
                                'errors' =>false);
                            return Response::json($data, 200);

                        } else {
                            $data = array(
                                'msj' => "No permitido",
                                'errors' =>true);
                            return Response::json($data, 400);
                        }

                    } else {
                        $monto = $reserva->monto_por_pagar;
                        $monto -= $monto_pago;
                        if ($tipo_pago == "Pago habitacion") {
                            $pago_habitacion =  Pago::where('reserva_id', $reserva_id)->where('tipo', 'Pago habitacion')->get();
                            if (empty($pago_habitacion)) {
                                $total_habitacion_pago = 0;

                            } else {
                                $total_habitacion_pago = 0;
                                foreach ($pago_habitacion as $pago) {
                                $total_habitacion_pago += $pago->monto_pago;
                                }
                            }

                            if ($total_habitacion_pago != $reserva->monto_alojamiento) {
                                if ($monto_pago <= $reserva->monto_por_pagar) {
                                    if(isset($pago['monto_pago']) && !is_null($monto_equivalente)){
                                        $pago                        = new Pago();
                                        $pago->monto_pago            = $monto_pago;
                                        $pago->monto_equivalente     = $monto_equivalente; 
                                        $pago->tipo                  = $tipo_pago;
                                        $pago->numero_operacion      = $numero_operacion;
                                        $pago->tipo_comprobante_id   = $tipo_comprobante_id;
                                        $pago->metodo_pago_id        = $metodo_pago;
                                        $pago->tipo_moneda_id        = $tipo_moneda_id;
                                        if ($metodo_pago == 4) {
                                            $pago->numero_cheque     = $numero_cheque;
                                        }
                                        $pago->reserva_id            = $reserva->id;
                                        $pago->caja_id               = $caja_abierta->id;
                                        if ($metodo_pago != 2) {
                                            $pago->estado            = 1;
                                        }
                                        $pago->save();
                                  
                                    } else {
                                        $pago                        = new Pago();
                                        $pago->monto_pago            = $monto_pago;
                                        $pago->monto_equivalente     = $monto_pago; 
                                        $pago->tipo                  = $tipo_pago;
                                        $pago->numero_operacion      = $numero_operacion;
                                        $pago->tipo_comprobante_id   = $tipo_comprobante_id;
                                        $pago->metodo_pago_id        = $metodo_pago;
                                        $pago->tipo_moneda_id        = $tipo_moneda_id;
                                        if($metodo_pago == 4){
                                            $pago->numero_cheque     = $numero_cheque;
                                        }
                                        $pago->reserva_id            = $reserva->id;
                                        $pago->caja_id               = $caja_abierta->id;
                                        if ($metodo_pago != 2) {
                                            $pago->estado            = 1;
                                        }
                                        $pago->save();
                                    }

                                    $pagos_reserva = Pago::where('reserva_id', $reserva_id)->where('metodo_pago_id', 2)->first();

                                    if ($reserva->estado_reserva_id == 1 ) {
                                        $reserva->update(array('monto_por_pagar' => $monto, 'estado_reserva_id' => 2));
                                    }

                                    if (!is_null($pagos_reserva)) {

                                        if ($reserva->estado_reserva_id == 5 && $monto == 0) {
                                            $reserva->update(array('monto_por_pagar' => $monto, 'estado_reserva_id' => 5));
                                        } else {
                                            $reserva->update(array('monto_por_pagar' => $monto));
                                            // $pagos_reserva->update(array('estado' => 1));
                                        }

                                    } else {

                                        if ($reserva->estado_reserva_id == 5 && $monto == 0) {
                                            $reserva->update(array('monto_por_pagar' => $monto, 'estado_reserva_id' => 4));
                                        } else {
                                            $reserva->update(array('monto_por_pagar' => $monto));
                                        }
                                    }
                                } else {
                                    $data = array(
                                        'msj' => "El monto a pagar no corresponde",
                                        'errors' =>true);
                                    return Response::json($data, 400);
                                }
                            } else {
                                $data = array(
                                    'msj' => "La habitación ya fue pagada",
                                    'errors' =>true);
                                return Response::json($data, 400);
                            }
                        }
                    }

                    if ($tipo_pago == "Pago consumos") {
                        $consumos = HuespedReservaServicio::where('reserva_id', $reserva_id)->where('estado', 'Por pagar')->get();
                        if (!empty($consumos)) {
                            $monto_consumos = 0;  // total monto consumos por pagar
                            foreach ($consumos as $consumo){
                                $monto_consumos += $consumo->precio_total;    
                            }

                            if ($monto_consumos <= $reserva->monto_por_pagar) {
                                if (isset($pago['monto_pago']) && !is_null($monto_equivalente)) {
                                    $pago                        = new Pago();
                                    $pago->monto_pago            = $monto_pago;
                                    $pago->monto_equivalente     = $monto_equivalente; 
                                    $pago->tipo                  = $tipo_pago;
                                    $pago->numero_operacion      = $numero_operacion;
                                    $pago->tipo_comprobante_id   = $tipo_comprobante_id;
                                    $pago->metodo_pago_id        = $metodo_pago;
                                    $pago->tipo_moneda_id        = $tipo_moneda_id;
                                    if ($metodo_pago == 4) {
                                        $pago->numero_cheque     = $numero_cheque;
                                    }
                                    $pago->reserva_id            = $reserva->id;
                                    $pago->caja_id               = $caja_abierta->id;
                                    $pago->estado                = 1;
                                    $pago->save();
                                      
                                } else {
                                    $pago                        = new Pago();
                                    $pago->monto_pago            = $monto_pago;
                                    $pago->monto_equivalente     = $monto_pago; 
                                    $pago->tipo                  = $tipo_pago;
                                    $pago->numero_operacion      = $numero_operacion;
                                    $pago->tipo_comprobante_id   = $tipo_comprobante_id;
                                    $pago->metodo_pago_id        = $metodo_pago;
                                    $pago->tipo_moneda_id        = $tipo_moneda_id;
                                    if ($metodo_pago == 4) {
                                        $pago->numero_cheque     = $numero_cheque;
                                    }
                                    $pago->reserva_id            = $reserva->id;
                                    $pago->caja_id               = $caja_abierta->id;
                                    $pago->estado                = 1;
                                    $pago->save();
                                }

                                foreach ($consumos as $consumo){
                                    $consumo->update(array('estado' => 'Pagado', 'pago_id' => $pago->id));
                                }

                                $pagos_reserva = Pago::where('reserva_id', $reserva_id)->where('metodo_pago_id', 2)->first();


                                if (!is_null($pagos_reserva)) {

                                    if ($reserva->estado_reserva_id == 5 && $monto == 0) {
                                        $reserva->update(array('monto_por_pagar' => $monto, 'estado_reserva_id' => 5));
                                    } else {
                                        $reserva->update(array('monto_por_pagar' => $monto));
                                        $pagos_reserva->update(array('estado' => 1));
                                    }

                                } else {

                                    if ($reserva->estado_reserva_id == 5 && $monto == 0) {
                                        $reserva->update(array('monto_por_pagar' => $monto, 'estado_reserva_id' => 4));
                                    } else {
                                        $reserva->update(array('monto_por_pagar' => $monto));
                                    }
                                }
                            } else {
                                $data = array(
                                    'msj' => "El monto a pagar no corresponde",
                                    'errors' =>true);
                                return Response::json($data, 400);
                            }
                        } else {
                            $data = array(
                                'msj' => "No hay consumos por pagar",
                                'errors' =>true);
                            return Response::json($data, 400);
                        }
                    } 
                } else {
                    $retorno = array(
                        'msj'    => "Reserva no encontrada",
                        'errors' => true);
                    return Response::json($retorno, 404);
                }
            }
        } else {
            $retorno = array(
                'msj'    => "No hay caja abierta",
                'errors' => true);
            return Response::json($retorno, 400);
        }
        $data = array(
            'msj' => "Pago ingresado satisfactoriamente",
            'errors' =>false);
        return Response::json($data, 201);
    }


    public function pagoConsumo(Request $request)
    {
        if ($request->has('propiedad_id')) {
            $propiedad_id = $request->get('propiedad_id');
            $propiedad    = Propiedad::where('id', $propiedad_id)->first();
            if(is_null($propiedad)){
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
            $reserva_id    = $request->input('reserva_id');
            $reserva       = Reserva::where('id', $reserva_id)->with('pagos')->first();
            if(is_null($reserva)){
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

        $caja_abierta  = Caja::where('propiedad_id', $propiedad->id)->where('estado_caja_id', 1)->first();
        if (!is_null($caja_abierta)) {
            if ($request->has('servicio_id') && $request->has('monto_pago') && $request->has('tipo_moneda_id')) {
                $servicios           = $request->input('servicio_id');
                $monto_pago          = $request->input('monto_pago');
                $tipo_comprobante_id = $request->input('tipo_comprobante_id');
                $numero_operacion    = $request->input('numero_operacion');
                $metodo_pago         = $request->input('metodo_pago_id');
                $numero_cheque       = $request->input('numero_cheque');
                $tipo_moneda_id      = $request->input('tipo_moneda_id');

                $total_consumos      = 0;
                $monto_por_pagar     = $reserva->monto_por_pagar;
                $consumos_por_pagar  = [];
                foreach($servicios as $servicio){
                    $consumo =  HuespedReservaServicio::where('id', $servicio)->first();
                    if($consumo->estado == 'Por pagar'){
                        array_push($consumos_por_pagar, $consumo);
                    }
                }

                if (count($consumos_por_pagar) > 0) {
                    foreach ($consumos_por_pagar as  $consumo) {
                        $total_consumos += $consumo->precio_total;
                    }
                } else {
                    $retorno = array(
                        'msj'    => "Los consumos ya fueron pagados",
                        'errors' => true);
                    return Response::json($retorno, 400);
                }

                $total = $monto_por_pagar - $monto_pago;
                if ($monto_pago <= $reserva->monto_por_pagar) {

                    $pagos_reserva = Pago::where('reserva_id', $reserva_id)->where('metodo_pago_id', 2)->first();

                    if (!is_null($pagos_reserva)) {
                        if ($reserva->estado_reserva_id == 5 && $total == 0) {
                            $reserva->update(array('monto_por_pagar' => $total, 'estado_reserva_id' => 5));
                        } else {
                            $reserva->update(array('monto_por_pagar' => $total));
                        }
                    } else {
                        if ($reserva->estado_reserva_id == 5 && $total == 0) {
                            $reserva->update(array('monto_por_pagar' => $total, 'estado_reserva_id' => 4));
                        } else {
                            $reserva->update(array('monto_por_pagar' => $total));
                        }
                    }

                    if ($request->has('monto_pago') && $request->has('monto_equivalente')) {
                        $monto_equivalente           = $request->input('monto_equivalente');

                        $pago                        = new Pago();
                        $pago->monto_pago            = $monto_pago;
                        $pago->monto_equivalente     = $monto_equivalente; 
                        $pago->tipo                  = "Pago consumos";
                        $pago->numero_operacion      = $numero_operacion;
                        $pago->tipo_comprobante_id   = $tipo_comprobante_id;
                        $pago->metodo_pago_id        = $metodo_pago;
                        $pago->tipo_moneda_id        = $tipo_moneda_id;
                        if ($metodo_pago == 4) {
                            $pago->numero_cheque     = $numero_cheque;
                        }
                        $pago->reserva_id            = $reserva->id;
                        $pago->caja_id               = $caja_abierta->id;
                        $pago->estado                = 1;
                        $pago->save();
                          
                    } else {
                        $pago                        = new Pago();
                        $pago->monto_pago            = $monto_pago;
                        $pago->monto_equivalente     = $monto_pago; 
                        $pago->tipo                  = "Pago consumos";
                        $pago->numero_operacion      = $numero_operacion;
                        $pago->tipo_comprobante_id   = $tipo_comprobante_id;
                        $pago->metodo_pago_id        = $metodo_pago;
                        $pago->tipo_moneda_id        = $tipo_moneda_id;
                        if ($metodo_pago == 4) {
                            $pago->numero_cheque     = $numero_cheque;
                        }
                        $pago->reserva_id            = $reserva->id;
                        $pago->caja_id               = $caja_abierta->id;
                        $pago->estado                = 1;
                        $pago->save();

                    }

                    foreach ($servicios as $servicio) {
                       $consumo =  HuespedReservaServicio::where('id', $servicio)->first();
                       $consumo->update(array('estado' => 'Pagado', 'pago_id' => $pago->id));
                    }

                    $retorno = array(
                        'msj' => "Pago realizado correctamente",
                        'errors' =>false);
                    return Response::json($retorno, 201);

                } else {
                    $retorno = array(
                        'msj'    => "No se puede realizar el pago",
                        'errors' => true);
                    return Response::json($retorno, 400);
                }
                
            } else {
                $retorno = array(
                    'msj'    => "La solicitud esta incompleta",
                    'errors' => true);
                return Response::json($retorno, 400);
            }
        } else {
            $retorno = array(
                'msj'    => "No hay caja abierta",
                'errors' => true);
            return Response::json($retorno, 400);
        }
        

    }


    public function panel(Request $request)
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

        if ($request->has('fecha_actual') && $request->has('fecha_inicio') && $request->has('fecha_fin')) {
            $fecha        = $request->input('fecha_actual');
            $fecha_inicio = $request->input('fecha_inicio');
            $fecha_fin    = $request->input('fecha_fin');
        } else {
            $retorno = array(
                'msj'    => "Solicitud incompleta",
                'errors' => true);
            return Response::json($retorno, 400);
        }
        
        $fecha_hoy = new Carbon($fecha);

        $startDate = Carbon::today()->startOfDay();
        $endDate   = Carbon::today()->endOfDay();


        // Reservas de hoy
        $reservas_hoy = Reserva::select('reservas.id','numero_reserva', 'checkin', 'checkout', 'habitacion_id', 'observacion', 'estado_reserva_id', 'estado_reserva.nombre as estado', 'cliente_id')
        ->whereHas('habitacion', function($query) use($id){
            $query->where('propiedad_id', $id);})
        ->where('checkin', '<=' , $fecha)
        ->where('checkout', '>=', $fecha)
        ->with(['cliente' => function ($q){
            $q->select('clientes.id','clientes.nombre' ,'apellido', 'paises.nombre as pais' ,'regiones.nombre as region', 'ciudad', 'direccion', 'telefono', 'email')
                ->join('paises', 'paises.id', '=' ,'pais_id')
                ->join('regiones', 'regiones.id', '=' ,'region_id');}])
        ->with(['habitacion' => function ($q){
            $q->select('habitaciones.id', 'habitaciones.nombre', 'tipo_habitacion.nombre as tipo_habitacion')
                ->join('tipo_habitacion', 'tipo_habitacion.id', '=' ,'tipo_habitacion_id');}])
        ->join('estado_reserva', 'estado_reserva.id', '=', 'estado_reserva_id')
        ->get();

        // $reservas_hoy = Reserva::whereHas('habitacion', function($query) use($id){
        //             $query->where('propiedad_id', $id);
        // })->where('checkin', '<=' , $fecha)->where('checkout', '>=', $fecha)->with('habitacion.tipoHabitacion')->with('huespedes')->with('cliente.pais', 'cliente.region')->with('estadoReserva')->get();

        $entradas              = 0;
        $salidas               = 0;
        $habitaciones_ocupadas = 0;
        $entradas_hoy          = [];
        $salidas_hoy           = [];
        foreach ($reservas_hoy as $reserva) {
            if ($reserva->checkin == $fecha_hoy && $reserva->estado_reserva_id !=6 && $reserva->estado_reserva_id !=7) {
                if ($reserva->estado_reserva_id == 1 || $reserva->estado_reserva_id == 2 ) {
                    array_push($entradas_hoy, $reserva);
                } 
                $entradas++;
            }

            if ($reserva->checkout == $fecha_hoy && $reserva->estado_reserva_id !=1 && $reserva->estado_reserva_id !=2 && $reserva->estado_reserva_id !=6 && $reserva->estado_reserva_id !=7) {
                if ($reserva->estado_reserva_id == 3) {
                    array_push($salidas_hoy, $reserva);
                } 
                $salidas++;
            }

            if ($reserva->estado_reserva_id == 3) {
                if ($reserva->checkout >= $fecha_hoy) {
                $habitaciones_ocupadas++;
                }
            }
        }

        //Actividades del dia

        $reservas_dia = Reserva::select('reservas.id','numero_reserva', 'monto_total' ,'noches' ,'checkin', 'checkout' ,'clientes.nombre as nombre_cliente', 'clientes.apellido as apellido_cliente' ,'habitacion_id', 'observacion', 'estado_reserva.nombre as estado')
        ->WhereHas('habitacion', function($query) use($id){
            $query->where('propiedad_id', $id);})
        ->whereBetween('reservas.created_at', [$startDate, $endDate])
        ->join('clientes', 'clientes.id', '=', 'cliente_id')
        ->join('estado_reserva', 'estado_reserva.id', '=', 'estado_reserva_id')
        ->with(['habitacion' => function ($q){
            $q->select('habitaciones.id', 'habitaciones.nombre', 'tipo_habitacion.nombre as tipo_habitacion')
              ->join('tipo_habitacion', 'tipo_habitacion.id', '=' ,'tipo_habitacion_id');}])
        ->get();


        // $reservas_dia = Reserva::whereHas('habitacion', function($query) use($id){

        //             $query->where('propiedad_id', $id);

        // })->whereBetween('created_at', [$startDate, $endDate])->with('habitacion.tipoHabitacion')->with('huespedes')->with('cliente')->with('estadoReserva')->with('metodoPago')->with('tipoFuente')->get();


        //reservas no show

        $reservas_no_show = Reserva::select('reservas.id','numero_reserva', 'checkin', 'clientes.nombre as nombre_cliente', 'clientes.apellido as apellido_cliente' ,'habitacion_id', 'observacion', 'estado_reserva.nombre as estado')
        ->whereHas('habitacion', function($query) use($id){
            $query->where('propiedad_id', $id);})
        ->where('checkin', '<' , $fecha_hoy)
        ->whereBetween('estado_reserva_id', [1,2])
        ->with(['habitacion' => function ($q){
            $q->select('habitaciones.id', 'habitaciones.nombre', 'tipo_habitacion.nombre as tipo_habitacion')
              ->join('tipo_habitacion', 'tipo_habitacion.id', '=' ,'tipo_habitacion_id');}])
        ->join('clientes', 'clientes.id', '=', 'cliente_id')
        ->join('estado_reserva', 'estado_reserva.id', '=', 'estado_reserva_id')
        ->get();


        // $reservas = Reserva::select('reservas.id', 'numero_reserva' ,'checkin', 'habitacion_id', 'estado_reserva_id' ,'checkout','ocupacion', 'monto_total','estado_reserva.nombre as estado' ,'cliente_id', 'clientes.nombre as nombre_cliente', 'clientes.apellido as apellido_cliente', 'noches', 'tipo_moneda.nombre as nombre_moneda', 'cantidad_decimales', 'monto_por_pagar')
        // ->whereHas('habitacion', function($query) use($id){
        //     $query->where('propiedad_id', $id);})
        // ->with(['huespedes' => function ($q){
        //     $q->select('huespedes.id', 'nombre', 'apellido');}])
        // ->with('habitacion.tipoHabitacion')
        // ->with('cliente.pais', 'cliente.region')
        // ->join('clientes', 'clientes.id','=','cliente_id')
        // ->join('tipo_moneda', 'tipo_moneda.id', '=', 'tipo_moneda_id')
        // ->join('estado_reserva', 'estado_reserva.id', '=', 'estado_reserva_id')
        // ->orderBy('reservas.id', 'desc')
        // ->take(50)
        // ->get();



        //PORCENTAJE OCUPACION GRAFICO

        $reservas = Reserva::select('reservas.id','numero_reserva', 'checkin', 'checkout')
        ->whereHas('habitacion', function($query) use($id){
            $query->where('propiedad_id', $id);})
        ->where('checkin','>=' ,$fecha_inicio)
        ->where('checkout', '<=', $fecha_fin)
        ->whereIn('estado_reserva_id', [3,4,5,6])
        ->get();

        // $reservas = Reserva::whereHas('habitacion', function($query) use($id){

        //             $query->where('propiedad_id', $id);

        // })->where('checkin','>=' ,$fecha_inicio)->where('checkout', '<=', $fecha_fin)->where('estado_reserva_id', '!=', 1)->where('estado_reserva_id', '!=', 2)->where('estado_reserva_id', '!=', 6)->where('estado_reserva_id', '!=', 7)->get();

        $numero_habitaciones = $propiedad->numero_habitaciones;
        $auxInicio           = new Carbon($fecha_inicio);
        $auxFin              = new Carbon($fecha_fin);
        $ocupacion           = [];
        while ($auxInicio <= $auxFin) {
            $suma = 0;
            foreach ($reservas as $reserva) {
                if ($reserva->checkin <= $auxInicio && $reserva->checkout > $auxInicio) {
                    $suma++;
                }
            }
        $porcentaje               = ($suma*100) / $numero_habitaciones;
        $fecha_ocupacion['date']  = $auxInicio->format('Y-m-d');
        $fecha_ocupacion['value'] = round($porcentaje);

        array_push($ocupacion, $fecha_ocupacion);
        unset($fecha_ocupacion);

        $auxInicio->addDay();
        }

        $suma_noches = 0;
        foreach ($reservas_dia as $reserva_dia) {
          $noches    = $reserva_dia->noches;
          $suma_noches += $noches;
        }

        $data = array(

            'cantidad_entradas'     => $entradas,
            'cantidad_salidas'      => $salidas,
            'habitaciones_ocupadas' => $habitaciones_ocupadas,
            'entradas'              => $entradas_hoy,
            'salidas'               => $salidas_hoy,
            'reservas_no_show'      => $reservas_no_show,
            'cantidad_reservas_dia' => count($reservas_dia),
            'suma_noches'           => $suma_noches,
            'reservas_dia'          => $reservas_dia,  
            'porcentaje_ocupacion'  => $ocupacion,
          );

        return $data;
    }


    public function calendario(Request $request)
    {
        if ($request->has('propiedad_id') && $request->has('fecha_inicio') && $request->has('fecha_fin') && $request->has('ancho_calendario') && $request->has('ancho_celdas') && $request->has('cantidad_dias')) {
            $id               = $request->input('propiedad_id');
            $fecha_inicio     = $request->input('fecha_inicio');
            $fecha_fin        = $request->input('fecha_fin');
            $ancho_calendario = $request->input('ancho_calendario');
            $ancho_celdas     = $request->input('ancho_celdas');
            $cantidad_dias    = $request->input('cantidad_dias');
            $calendario       = [];
        } else {
            $retorno = array(
                'msj'    => "La solicitud esta incompleta",
                'errors' => true);
            return Response::json($retorno, 400);
        }

        $fechas = [$fecha_inicio, $fecha_fin];
        $tipos  = TipoHabitacion::whereHas('habitaciones', function($query) use($id){
            $query->where('propiedad_id', $id);
        })->with(['habitaciones' => function ($q){
            $q->select('id', 'nombre', 'tipo_habitacion_id');}])->get();

        $reservas = Reserva::select('reservas.id', 'checkin', 'checkout', 'habitacion_id', 'estado_reserva_id', 'cliente_id', 'nombre', 'apellido', 'noches')->whereHas('habitacion', function($query) use($id){
            $query->where('propiedad_id', $id);})
        ->join('clientes', 'clientes.id','=','cliente_id')
        ->whereBetween('checkin', $fechas)->whereIn('estado_reserva_id', [1,2,3,4,5])->get();

        $reservas_checkout = Reserva::select('reservas.id', 'checkin', 'checkout', 'habitacion_id', 'estado_reserva_id', 'cliente_id', 'nombre', 'apellido', 'noches')->whereHas('habitacion', function($query) use($id){
            $query->where('propiedad_id', $id);})
        ->join('clientes', 'clientes.id','=','cliente_id')
        ->whereBetween('checkout', $fechas)->whereIn('estado_reserva_id', [1,2,3,4,5])->get();

        $habitaciones_tipo = [];

        foreach ($tipos as $tipo) {
            $habitaciones = $tipo->habitaciones;
            $nombre_tipo  = $tipo->nombre;
            $nombre       = [ 'nombre' => $nombre_tipo, 'header' => 1];
            array_push($habitaciones_tipo, $nombre);
            foreach ($habitaciones as $habitacion) {
                array_push($habitaciones_tipo, $habitacion);
            }
        }

        $reservas_calendario = [];
       
        foreach ($reservas as $reserva) {
            $inicio           = new Carbon($fecha_inicio);
            $checkout         = $reserva->checkout;
            $fin              = new Carbon($checkout);
            $diferencia       = $inicio->diffInDays($fin);
            $checkin          = new Carbon($reserva->checkin);
            $posicion_checkin = $inicio->diffInDays($checkin) + 1;
            if ($diferencia <= ($cantidad_dias -1 )) {
                $noches         = $reserva->noches;
                $reserva->left  = ($posicion_checkin * $ancho_celdas)-30;
                $reserva->right = (($ancho_calendario - $reserva->left - ($noches * $ancho_celdas)) + $ancho_celdas)-60;
                array_push($reservas_calendario, $reserva);
             } else {
                $reserva->left  = ($posicion_checkin * $ancho_celdas)-30;
                $reserva->right = 0;
                array_push($reservas_calendario, $reserva);
            }
        }

        foreach ($reservas_checkout as $reserva_checkout) {
            $checkin           = $reserva_checkout->checkin;
            $in                = new Carbon($checkin);
            $inicio            = new Carbon($fecha_inicio);
            $checkout          = new Carbon($reserva_checkout->checkout);
            $posicion_checkout = $checkout->diffInDays($inicio) + 1;

            if ($in < $inicio ) {
                $reserva_checkout->left  = 0;
                $reserva_checkout->right = (($ancho_calendario - ( $ancho_celdas * $posicion_checkout)) + $ancho_celdas)-30;
                array_push($reservas_calendario, $reserva_checkout);
            }
        }

        array_push($calendario, $habitaciones_tipo);
        array_push($calendario, $reservas_calendario);

        return $calendario;

    }


    public function getPagoReserva(Request $request)
    {
        if($request->has('reserva_id')){
          $id        = $request->input('reserva_id');
          $reserva   = Reserva::where('id', $id)->first();
          if(is_null($reserva)){
            $retorno = array(
               'msj'    => "Reserva no encontrada",
               'errors' => true);
            return Response::json($retorno, 404);
          }
        }else{
          $retorno = array(
              'msj'    => "No se envia reserva_id",
              'errors' => true);
          return Response::json($retorno, 400);
        }

        $reservas = Reserva::where('id', $id)->with(['huespedes.servicios' => function ($q) use($id) {
            $q->wherePivot('reserva_id', $id);}])
        ->with('habitacion.tipoHabitacion')
        ->with('cliente.pais','cliente.region','tipoMoneda' ,'tipoFuente', 'metodoPago','estadoReserva','pagos.tipoComprobante','pagos.tipoMoneda', 'pagos.metodoPago')
        ->with('huespedes.pais', 'huespedes.region')
        ->get();

        foreach ($reservas as $reserva){
            foreach ($reserva['huespedes'] as $huesped) {
                $huesped->consumo_total = 0;
                foreach ($huesped['servicios'] as $servicio) {
                    $huesped->consumo_total += $servicio->pivot->precio_total;
                }
            }
        }

        $data = ['reservas' => $reservas];

        return $data;

    }



    public function show($id)
    {
        $reserva = Reserva::where('id', $id)
        ->with(['huespedes.servicios' => function ($q) use($id) {
            $q->wherePivot('reserva_id', $id);}])
        ->with('habitacion.tipoHabitacion')
        ->with('cliente.pais','cliente.region','tipoMoneda' ,'tipoFuente', 'metodoPago','estadoReserva','pagos.tipoComprobante','pagos.tipoMoneda', 'pagos.metodoPago')
        ->with('huespedes.pais', 'huespedes.region')
        ->first();

       if(!is_null($reserva)){
            foreach ($reserva['huespedes'] as $huesped) {
                $huesped->consumo_total = 0;
                foreach ($huesped['servicios'] as $servicio) {
                    $huesped->consumo_total += $servicio->pivot->precio_total;
                }
            }
            return $reserva;
            
       }else{
            $data = array(
                'msj' => "Reserva no encontrada",
                'errors' => true);
            return Response::json($data, 404);
       }

    }

    public function anularReservas(Request $request)
    {
        if ($request->has('reservas')) {
            $reservas = $request->get('reservas');
        } else {
            $retorno = array(
                'msj'    => "No se envia propiedad_id",
                'errors' => true);
            return Response::json($retorno, 400);
        }

        if ($request->has('observacion')) {
            $observacion = $request->get('observacion');
        } else {
            $retorno = array(
                'msj'    => "No se envia observación",
                'errors' => true);
            return Response::json($retorno, 400);
        }

        foreach ($reservas as $reserva) {
            $id      = $reserva;
            $reserva = Reserva::where('id', $id)->first();
            if (!is_null($reserva)) {
                $reserva->update(array('estado_reserva_id' => 6, 'observacion' => $observacion));
            } else {
                $retorno  = array(
                    'msj'    => "Propiedad no encontrada",
                    'errors' => true);
                return Response::json($retorno, 404);
            }
        }

          $retorno = [
              'errors' => false,
              'msj'    => 'Reservas anuladas satisfactoriamente',];
          return Response::json($retorno, 201);
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





    public function getTipoFuente()
    {
        $TipoFuente = TipoFuente::all();
        
        return $TipoFuente;
    }

    public function getMetodoPago()
    {
        $MetodoPago = MetodoPago::all();

        $respuesta = [
        'Metodo_pago' => $MetodoPago,
        ];

        return $respuesta;
    }


    public function getEstadoReserva()
    {
        $EstadoReserva = EstadoReserva::all();

        $respuesta = [
        'Estado_reserva' => $EstadoReserva,
        ];

        return $respuesta;
    }


    public function getTipoComprobante()
    {

      $TipoComprobante = TipoComprobante::all();

      $respuesta = [
      'Tipo_comprobante' => $TipoComprobante,
      ];

      return $respuesta;



    }


}


