<?php

namespace App\Http\Controllers;

use App\ClasificacionMoneda;
use App\Http\Controllers\Controller;
use App\Precio;
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
use Illuminate\Http\Request;
use Response;
use Validator;
use \Carbon\Carbon;

class PropiedadController extends Controller
{

        public function reportes(Request $request){




            $propiedad_id = $request->input('propiedad_id');
            $propiedad = Propiedad::where('id', $request->input('propiedad_id'))->first();

            $inicio       = new Carbon($request->input('fecha_inicio'));
            $zona_horaria = ZonaHoraria::where('id', $propiedad->zona_horaria_id)->first();
            $pais         = $zona_horaria->nombre;
            $fecha_inicio = Carbon::createFromFormat('Y-m-d H:i:s', $inicio, $pais)->tz('UTC');


            if ($request->has('fecha_fin')) {
            
            $fin          = new Carbon($request->input('fecha_fin'));
            $fechaFin     = $fin->addDay();
            $fecha_fin    = Carbon::createFromFormat('Y-m-d H:i:s', $fechaFin, $pais)->tz('UTC');


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


/*                    $paises = Pais::whereHas('huespedes.reservas.habitacion', function ($query) use ($propiedad_id) {
                            $query->where('propiedad_id', $propiedad_id);
                        })->where(function ($query) use ($propiedad_id, $auxInicio, $auxFin) {

                        $query->WhereHas('huespedes.reservas', function ($query) use ($auxInicio, $auxFin) {
                            $query->where('reservas.checkin', '>=' ,$auxInicio)->where('reservas.checkin', '<' , $auxFin);
                        });
                        $query->orWhereHas('huespedes.reservas', function ($query) use ($auxInicio, $auxFin) {
                            $query->where('reservas.checkin', '<=' ,$auxInicio)->where('reservas.checkout', '>' , $auxInicio);
                        });

                        
                    })->where('id', '!=', $propiedad->pais_id )->get();*/


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


/*                    $reservas = Reserva::whereHas('habitacion', function($query) use($propiedad_id){
                        $query->where('propiedad_id', $propiedad_id);
                    })
                    ->where(function($query) use ($auxInicio,$auxFin){
                        $query->where('checkin', '>=', $auxInicio);
                        $query->where('checkin', '<',  $auxFin);
                    })
                    ->orWhere(function($query) use ($auxInicio,$auxFin){
                        $query->where('checkin', '<=', $auxInicio);
                        $query->where('checkout', '>',  $auxInicio);
                    })
                    ->with('huespedes')
                    ->get();*/


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


/*                    $paises = Pais::whereHas('huespedes.reservas.habitacion', function ($query) use ($propiedad_id) {
                            $query->where('propiedad_id', $propiedad_id);
                        })->where(function ($query) use ($propiedad_id, $auxInicio, $auxFin) {

                        $query->WhereHas('huespedes.reservas', function ($query) use ($auxInicio, $auxFin) {
                            $query->where('reservas.checkin', '>=' ,$auxInicio)->where('reservas.checkin', '<' , $auxFin);
                        });
                        $query->orWhereHas('huespedes.reservas', function ($query) use ($auxInicio, $auxFin) {
                            $query->where('reservas.checkin', '<=' ,$auxInicio)->where('reservas.checkout', '>' , $auxInicio);
                        });

                    })->where('id', '!=', $propiedad->pais_id )->get();*/

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
                        
                        $extranjeros = [ 'nombre' => $pais->nombre, 'llegadas' => $huespedes, 'pernoctacion' => ($huespedes * $noches)];
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
                        
                        $residentes_pais = [ 'nombre' => $region->nombre, 'llegadas' => $huespedes, 'pernoctacion' => ($huespedes * $noches)];
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

            $propiedad             = Propiedad::where('id', $request->input('propiedad_id'))->with('habitaciones')->with('servicios')->first();
            $cantidad_habitaciones = count($propiedad->habitaciones);
            $cantidad_servicios    = count($propiedad->servicios);

            if (!is_null($propiedad)) {

                $monedas = $request->input('monedas');

                foreach ($monedas as $moneda) {

                    $clasificacion_moneda = $moneda['clasificacion_moneda_id'];
                    $tipo_moneda          = $moneda['tipo_moneda_id'];

                    $propiedad->clasificacionMonedas()->attach($clasificacion_moneda, ['tipo_moneda_id' => $tipo_moneda]);



                    if ($cantidad_servicios > 0) {

                        foreach ($propiedad->servicios as $servicio) {

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

            $moneda_id      = $request->input('moneda_id');
            $moneda         = PropiedadMoneda::where('id', $moneda_id)->first();
            $tipo_moneda_id = $moneda->tipo_moneda_id;
            $propiedad_id   = $moneda->propiedad_id;


            $precios_habitacion = PrecioTemporada::whereHas('temporada', function($query) use($propiedad_id){

                $query->where('propiedad_id', $propiedad_id);

            })->where('tipo_moneda_id', $tipo_moneda_id)->get();

            $precios_servicio = PrecioServicio::where('tipo_moneda_id', $tipo_moneda_id)->whereHas('servicio', function ($query) use ($propiedad_id) {

                $query->where('propiedad_id', $propiedad_id);

            })->get();

            foreach ($precios_habitacion as $precio) {
                
                $precio->delete();
            }

            foreach ($precios_servicio as $precio) {

                $precio->delete();
            }

            $moneda->delete();

            $retorno = [

                'errors' => false,
                'msj'    => 'Moneda eliminada satisfactoriamente',

            ];

            return Response::json($retorno, 202);

        } else {

            $retorno = array(

                'msj'    => "La solicitud esta incompleta",
                'errors' => true,
            );

            return Response::json($retorno, 400);

        }

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

            $moneda      = PropiedadMoneda::findOrFail($id);
            $tipo_moneda = $moneda->tipo_moneda_id;
            $moneda->update($request->all());
            $moneda->touch();

            $propiedad_id   = $moneda->propiedad_id;
            $tipo_moneda_id = $request->input('tipo_moneda_id');

            $precios_habitacion = Precio::where('tipo_moneda_id', $tipo_moneda)->whereHas('habitacion', function ($query) use ($propiedad_id) {

                $query->where('propiedad_id', $propiedad_id);

            })->get();

            $precios_servicio = PrecioServicio::where('tipo_moneda_id', $tipo_moneda)->whereHas('servicio', function ($query) use ($propiedad_id) {

                $query->where('propiedad_id', $propiedad_id);

            })->get();

            if($tipo_moneda != $tipo_moneda_id){
                foreach ($precios_habitacion as $precio) {


                $precio->update(array('precio_habitacion' => null, 'tipo_moneda_id' => $tipo_moneda_id));

                $habitacion = $precio->habitacion;

                $habitacion->update(array('estado_habitacion_id' => 2));


                }

                foreach ($precios_servicio as $precio) {

                 $precio->update(array('precio_servicio' => null, 'tipo_moneda_id' => $tipo_moneda_id));

                 $servicio = $precio->servicio;

                 $servicio->update(array('estado_servicio_id' => 2));

                }

            }

            $data = [

                'errors' => false,
                'msg'    => 'Moneda actualizada satisfactoriamente',

            ];

            return Response::json($data, 201);

        }

    }

    public function reportesDiario(Request $request)
    {

        if($request->has('propiedad_id')){

            $propiedad_id = $request->input('propiedad_id');
            $propiedad = Propiedad::where('id', $request->input('propiedad_id'))->first();

            if(!is_null($propiedad)){



                if($request->has('fecha')){

                   $fecha1 = $request->input('fecha');

                   $fecha2 = date ("Y-m-d", strtotime("+1 day", strtotime($fecha1)));
                    
                   $pagos = Pago::where('created_at','>=' , $fecha1)->where('created_at', '<' , $fecha2)->whereHas('reserva.habitacion', function($query) use($propiedad_id){

                    $query->where('propiedad_id', $propiedad_id);

                    })->get();




                    $pagos_particulares = Pago::where('created_at','>=' , $fecha1)->where('created_at', '<' , $fecha2)->whereHas('reserva.habitacion', function($query) use($propiedad_id){

                    $query->where('propiedad_id', $propiedad_id);

                    })->whereHas('reserva.cliente', function($query){

                    $query->where('tipo_cliente_id', 1);

                    })->with('reserva.cliente')->get();



                   $pagos_empresas= Pago::where('created_at','>=' , $fecha1)->where('created_at', '<' , $fecha2)->whereHas('reserva.habitacion', function($query) use($propiedad_id){

                    $query->where('propiedad_id', $propiedad_id);

                    })->whereHas('reserva.cliente', function($query){

                    $query->where('tipo_cliente_id', 2);

                    })->with('reserva.cliente')->get();








                   $reservas = Reserva::where('created_at' , '>=', $fecha1)->where('created_at', '<' , $fecha2)->whereHas('habitacion', function($query) use($propiedad_id){

                    $query->where('propiedad_id', $propiedad_id);

                    })->get();

                /* INGRESOS TOTALES DEL DIA  */

                   $ingresos_totales_dia = [];
                   $ingresos_habitacion = [];
                   $ingresos_consumos = [];
                   $ingresos_por_efectivo = [];
                   $ingresos_por_credito = [];
                   $ingresos_por_debito = [];
                   $ingresos_por_cheque = [];
                   $ingresos_por_tarjeta_credito = [];
                   $ingresos_por_transferencia = [];
                   $ingresos_por_particulares = [];
                   $ingresos_por_empresas = [];



                   foreach ($propiedad->tipoMonedas as $moneda) {

                      $tipo_moneda_id = $moneda->pivot->tipo_moneda_id;

                      $pagos_tipo_moneda = $pagos->where('tipo_moneda_id', $tipo_moneda_id);

                      $pagos_por_particulares = $pagos_particulares->where('tipo_moneda_id', $tipo_moneda_id);

                      $pagos_por_empresas = $pagos_empresas->where('tipo_moneda_id', $tipo_moneda_id);



                      $suma_pagos = 0;
                      $ingresos_por_habitacion = 0;
                      $ingresos_por_consumos = 0;
                      $ingresos_efectivo = 0;
                      $ingresos_credito = 0;
                      $ingresos_debito = 0;
                      $ingresos_cheque = 0;
                      $ingresos_tarjeta_credito = 0;
                      $ingresos_transferencia = 0;
                      $ingresos_particulares = 0;
                      $ingresos_empresas = 0;

                      foreach ($pagos_tipo_moneda as $pago) {

                          $suma_pagos += $pago->monto_equivalente;

                          if($pago->tipo == 'Pago habitacion'){

                            $ingresos_por_habitacion += $pago->monto_equivalente;


                          }elseif($pago->tipo == 'Pago consumos'){

                            $ingresos_por_consumos += $pago->monto_equivalente;


                          }elseif ($pago->tipo == 'Confirmacion de reserva') {
                            $ingresos_por_habitacion += $pago->monto_equivalente;

                          }elseif($pago->tipo == 'Pago reserva') {

                            $monto_pago = $pago->monto_pago;
                            $monto_equivalente = $pago->monto_equivalente;

                            $pagos_reserva = $pago->reserva->pagos;

                            if(!is_null($pagos_reserva)){
                                
                                    $reserva_monto_alojamiento = $pago->reserva->monto_alojamiento;

                                    $reserva_monto_consumos = $pago->reserva->monto_consumo;


                                    $pagos_habitacion_realizados = 0;
                                    $pagos_consumos_realizados = 0;
                                    foreach ($pagos_reserva as $pago_reserva) {
                                            
                                       if($pago_reserva->tipo == 'Pago habitacion'){
                                            $pagos_habitacion_realizados += $pago_reserva->monto_pago;


                                       }elseif ($pago_reserva->tipo == 'Pago consumos') {
                                            $pagos_consumos_realizados += $pago_reserva->monto_pago;
                                       }elseif ($pago_reserva->tipo == 'Confirmacion de reserva') {
                                            $pagos_habitacion_realizados += $pago_reserva->monto_pago;
                                       }

                                    }


                                    if($pago->reserva->tipo_moneda_id == $pago->tipo_moneda_id){

                                      $monto_pagado_habitacion = $reserva_monto_alojamiento - $pagos_habitacion_realizados;
                                      $monto_pagado_consumos = $reserva_monto_consumos - $pagos_consumos_realizados;

                                      $ingresos_por_habitacion += $monto_pagado_habitacion;
                                      $ingresos_por_consumos += $monto_pagado_consumos;



                                    }else{


                                      $monto_pagado_habitacion = $reserva_monto_alojamiento - $pagos_habitacion_realizados;
                                      $monto_pagado_consumos = $reserva_monto_consumos - $pagos_consumos_realizados;

                                      if($pago->reserva->tipo_moneda_id == 1){

                                          $conversion = round($monto_pago / $monto_equivalente);

                                          $ingresos_por_habitacion += ($monto_pagado_habitacion / $conversion);
                                          $ingresos_por_consumos += ($monto_pagado_consumos / $conversion);
                                        
                                      }elseif ($pago->reserva->tipo_moneda_id == 2) {

                                          $conversion = round($monto_equivalente/$monto_pago);

                                          $ingresos_por_habitacion += ($monto_pagado_habitacion * $conversion);
                                          $ingresos_por_consumos += ($monto_pagado_consumos * $conversion);
                                      }



                                    }

                            }



                          }

                          /*INGRESOS POR METODO PAGO */


                          if($pago->metodo_pago_id == 1){

                            $ingresos_efectivo += $pago->monto_equivalente;

                          }elseif($pago->metodo_pago_id == 2){
                            $ingresos_credito += $pago->monto_equivalente;


                          }elseif($pago->metodo_pago_id == 3) {

                            $ingresos_debito += $pago->monto_equivalente;

                          }elseif($pago->metodo_pago_id == 4) {
                            $ingresos_cheque += $pago->monto_equivalente;

                          }elseif($pago->metodo_pago_id == 5) {

                            $ingresos_tarjeta_credito += $pago->monto_equivalente;
                          }elseif($pago->metodo_pago_id == 6) {

                            $ingresos_transferencia += $pago->monto_equivalente;
                          }




                      }

                          /*INGRESOS POR TIPO DE CLIENTE*/
                      

                          /* CLIENTE PARTICULAR*/

                          foreach ($pagos_por_particulares as $pago) {
                              
                              $ingresos_particulares += $pago->monto_equivalente;

                            
                          }


                          
                            /* CLIENTE EMPRESA*/

                          foreach ($pagos_por_empresas as $pago) {
                              
                              $ingresos_empresas += $pago->monto_equivalente;

                          }





                    

                      $ingresos = ['monto' => $suma_pagos , 'tipo_moneda_id' => $tipo_moneda_id, 'nombre_moneda' => $moneda->nombre, 'cantidad_decimales' => $moneda->cantidad_decimales]; 
                      $ingresos_hab = ['monto' => $ingresos_por_habitacion,'tipo_moneda_id' => $tipo_moneda_id, 'nombre_moneda' => $moneda->nombre, 'cantidad_decimales' => $moneda->cantidad_decimales];
                      $ingresos_serv = ['monto' => $ingresos_por_consumos,'tipo_moneda_id' => $tipo_moneda_id, 'nombre_moneda' => $moneda->nombre, 'cantidad_decimales' => $moneda->cantidad_decimales];
                      $efectivo = ['monto' => $ingresos_efectivo,'tipo_moneda_id' => $tipo_moneda_id, 'nombre_moneda' => $moneda->nombre, 'cantidad_decimales' => $moneda->cantidad_decimales];
                      $credito = ['monto' => $ingresos_credito, 'tipo_moneda_id' => $tipo_moneda_id, 'nombre_moneda' => $moneda->nombre, 'cantidad_decimales' => $moneda->cantidad_decimales];
                      $debito = ['monto' => $ingresos_debito, 'tipo_moneda_id' => $tipo_moneda_id, 'nombre_moneda' => $moneda->nombre, 'cantidad_decimales' => $moneda->cantidad_decimales];
                      $cheque = ['monto' => $ingresos_cheque, 'tipo_moneda_id' => $tipo_moneda_id, 'nombre_moneda' => $moneda->nombre, 'cantidad_decimales' => $moneda->cantidad_decimales];
                      $tarjeta_credito = ['monto' => $ingresos_tarjeta_credito, 'tipo_moneda_id' => $tipo_moneda_id, 'nombre_moneda' => $moneda->nombre, 'cantidad_decimales' => $moneda->cantidad_decimales];
                      $transferencia = ['monto' => $ingresos_transferencia, 'tipo_moneda_id' => $tipo_moneda_id, 'nombre_moneda' => $moneda->nombre, 'cantidad_decimales' => $moneda->cantidad_decimales];
                      $particulares = ['monto' => $ingresos_particulares, 'tipo_moneda_id' => $tipo_moneda_id, 'nombre_moneda' => $moneda->nombre, 'cantidad_decimales' => $moneda->cantidad_decimales];
                      $empresas = ['monto' => $ingresos_empresas, 'tipo_moneda_id' => $tipo_moneda_id, 'nombre_moneda' => $moneda->nombre, 'cantidad_decimales' => $moneda->cantidad_decimales];


                      array_push($ingresos_totales_dia, $ingresos);
                      array_push($ingresos_habitacion, $ingresos_hab);
                      array_push($ingresos_consumos, $ingresos_serv);
                      array_push($ingresos_por_efectivo, $efectivo);
                      array_push($ingresos_por_credito, $credito);
                      array_push($ingresos_por_debito, $debito);
                      array_push($ingresos_por_cheque, $cheque);
                      array_push($ingresos_por_tarjeta_credito, $tarjeta_credito);
                      array_push($ingresos_por_transferencia, $transferencia);
                      array_push($ingresos_por_particulares, $particulares);
                      array_push($ingresos_por_empresas, $empresas);

                      
                }



                    /*RESERVAS POR TIPO DE FUENTE */

                     $pagina_web = count($reservas->where('tipo_fuente_id', 1));
                     $caminando = count($reservas->where('tipo_fuente_id', 2));
                     $telefono = count($reservas->where('tipo_fuente_id', 3));
                     $email = count($reservas->where('tipo_fuente_id', 4));
                     $redes_sociales = count($reservas->where('tipo_fuente_id', 5));
                     $expedia = count($reservas->where('tipo_fuente_id', 6));
                     $booking = count($reservas->where('tipo_fuente_id', 7));
                     $airbnb = count($reservas->where('tipo_fuente_id', 8));

                     /*RESERVAS ANULADAS*/

                    $reservas_anuladas = Reserva::where('updated_at' , '>=', $fecha1)->where('updated_at', '<' , $fecha2)->whereHas('habitacion', function($query) use($propiedad_id){

                    $query->where('propiedad_id', $propiedad_id);

                    })->where('estado_reserva_id', 6)->get();

                    /*RESERVAS NO SHOW*/

                    $reservas_no_show = Reserva::where('updated_at' , '>=', $fecha1)->where('updated_at', '<' , $fecha2)->whereHas('habitacion', function($query) use($propiedad_id){

                    $query->where('propiedad_id', $propiedad_id);

                    })->where('estado_reserva_id', 7)->get();



                  $data = [ 
                            'ingresos_totales'          => $ingresos_totales_dia,
                            'reservas_realizadas'       => count($reservas),
                            'reservas_anuladas'         => count($reservas_anuladas),
                            'reservas_no_show'          => count($reservas_no_show),
                            'ingresos_por_habitacion'   => $ingresos_habitacion,
                            'ingresos_por_servicios'    => $ingresos_consumos,


                            'ingresos_por_metodo_pago'  => [
                                                            ['nombre' => 'Efectivo', 'id' => 1, 'montos' =>$ingresos_por_efectivo],
                                                            ['nombre' => 'Credito', 'id' => 2,'montos' => $ingresos_por_credito],
                                                            ['nombre' => 'Debito', 'id' => 3,'montos' => $ingresos_por_debito],
                                                            ['nombre' => 'Cheque', 'id' => 4,'montos' => $ingresos_por_cheque],
                                                            ['nombre' => 'Tarjeta credito', 'id' => 5,'montos' => $ingresos_por_tarjeta_credito],
                                                            ['nombre' => 'Transferencia', 'id' => 6,'montos' => $ingresos_por_transferencia ]
                                                           ],

                            'reservas_por_fuente'       => [

                                                            ['nombre' => 'Pagina web',     'id' => 1, 'cantidad' => $pagina_web],
                                                            ['nombre' => 'Caminando',      'id' => 2, 'cantidad' => $caminando],
                                                            ['nombre' => 'Telefono',       'id' => 3, 'cantidad' => $telefono],
                                                            ['nombre' => 'Email',          'id' => 4, 'cantidad' => $email],
                                                            ['nombre' => 'Redes sociales', 'id' => 5, 'cantidad' => $redes_sociales],
                                                            ['nombre' => 'Expedia',        'id' => 6, 'cantidad' => $expedia],
                                                            ['nombre' => 'Booking',        'id' => 7, 'cantidad' => $booking],
                                                            ['nombre' => 'Airbnb',         'id' => 8, 'cantidad' => $airbnb]




                                                           ],
                            'ingresos_tipo_cliente'     =>[      
                                                            ['nombre'=> 'Particular' , 'id'=> 1, 'montos' => $ingresos_por_particulares],
                                                            ['nombre'=> 'Empresa' , 'id'=> 2, 'montos' => $ingresos_por_empresas]
                                                          ]

                            ]; 

                }//FIN IF


                return $data;



            }else{

                
                $retorno = array(

                    'msj'    => "Propiedad no encontrada",
                    'errors' => true,

                );

                return Response::json($retorno, 404);


            }




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
