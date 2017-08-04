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
use \Carbon\Carbon;
use Response;

class PDFController extends Controller
{
    

	public function estadoCuenta(Request $request){

		$reservas = $request['reservas'];
		$propiedad_id = $request->input('propiedad_id');
		$cliente_id = $request->input('cliente_id');
		/*$iva = $request->input('iva');*/



		$propiedad = Propiedad::where('id', $propiedad_id)->with('pais', 'region')->get();
		$cliente = Cliente::where('id', $cliente_id)->with('pais', 'region')->get();

		$propiedad_iva = 0;
		foreach ($propiedad as $prop) {
			
			$propiedad_iva = $prop->iva;

		}

		$reservas_pdf = [];
		$monto_alojamiento = 0;
		$consumo = 0;
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

        foreach ($reserva as $reserv) {
            if ($reserv->tipo_moneda_id == 1) {

                if ($reserv->iva == 1) {
                    $monto_reserva = $monto_alojamiento + $consumo;
                    $iva           = (($monto_reserva * $propiedad_iva) / 100);
                    $neto          = ($monto_reserva - $iva);
                    $total         = $neto + $iva;
                } else {

                    $total = $monto_alojamiento + $consumo;
                }

                
            }elseif($reserv->tipo_moneda_id == 2){

                $total = $monto_alojamiento + $consumo;
            }
        }


        foreach ($reserva as $reserv) {
            if ($reserv->tipo_moneda_id == 1) {
                $pdf = PDF::loadView('pdf.estado_cuenta', ['propiedad' => $propiedad,'consumo' => $consumo , 'cliente'=> $cliente ,'reservas_pdf'=> $reservas_pdf, 'neto' => $neto , 'iva' => $iva, 'total' => $total]);
                
            }elseif($reserv->tipo_moneda_id == 2){
                $pdf = PDF::loadView('pdf.estado_cuenta', ['propiedad' => $propiedad,'consumo' => $consumo , 'cliente'=> $cliente ,'reservas_pdf'=> $reservas_pdf,'total' => $total]);
                
            }
        }
		

		return $pdf->download('archivo.pdf');


	}

   public function pagos(Request $request)
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
            $auxFecha_inicio = $auxInicio->format('d/m/Y');
            $auxPagos = [];
            foreach ($pagos as $pago) {
                $created_at     = new Carbon($pago->created_at);
                $auxCreated_at  = $created_at->format('d/m/Y');
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

        $ingresos_totales = [];
        foreach ($propiedad->tipoMonedas as $moneda) {
            /*return $moneda->nombre;*/
            $suma_ingreso = 0;
            foreach ($fechas_pagos as $pago) {
                /*return $pago;*/
                foreach ($pago['ingresos'] as $ingreso) {
                    /*return $ingreso['nombre_moneda'];*/
                    if ($moneda->nombre == $ingreso['nombre_moneda']) {
                        $suma_ingreso += $ingreso['monto'];
                    }
                }
            }
            $sumaIngreso['nombre_moneda']       = $moneda->nombre;
            $sumaIngreso['monto']               = $suma_ingreso;
            $sumaIngreso['tipo_moneda_id']      = $moneda->pivot->tipo_moneda_id;
            $sumaIngreso['cantidad_decimales']  = $moneda->cantidad_decimales;
            array_push($ingresos_totales, $sumaIngreso);
        }

        $fecha_fin = $fecha_fin->subDay();
        $pdf = PDF::loadView('pdf.pagos', ['propiedad' => [$propiedad], 'pagos' => $fechas_pagos, 'fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin, 'ingresos_totales' => $ingresos_totales ]);

        return $pdf->download('archivo.pdf');

    }

    public function checkin(Request $request)
    {
        $reservas = $request['reservas'];
        $propiedad_id = $request->input('propiedad_id');
        $cliente_id = $request->input('cliente_id');

        $propiedad = Propiedad::where('id', $propiedad_id)->with('pais', 'region')->get();
        $cliente = Cliente::where('id', $cliente_id)->with('pais', 'region')->get();

        $propiedad_iva = 0;
        foreach ($propiedad as $prop) {
          
          $propiedad_iva = $prop->iva;

        }

        $reservas_pdf = [];
        $monto_alojamiento = 0;
        foreach($reservas as $id){

        $reserva = Reserva::where('id', $id)->where('cliente_id', $cliente_id)->with('cliente.pais', 'cliente.region')->with('tipoMoneda')->with('habitacion.tipoHabitacion')->get();

        if (count($reserva) == 0) {
          $retorno = array(
                'errors' => true,
                'msj'    => " Las reservas no pertenecen al mismo cliente"
            );
          return Response::json($retorno, 400);
        }

        foreach ($reserva as $ra) {
            $monto_alojamiento += $ra->monto_alojamiento;
        }

        array_push($reservas_pdf, $reserva);


        }

        foreach ($reserva as $reserv) {
            if ($reserv->tipo_moneda_id == 1) {

                if ($reserv->iva == 1) {
                    $monto_reserva = $monto_alojamiento;
                    $iva           = (($monto_reserva * $propiedad_iva) / 100);
                    $neto          = ($monto_reserva - $iva);
                    $total         = $neto + $iva;
                } else {

                    $total = $monto_alojamiento;
                }

                
            }elseif($reserv->tipo_moneda_id == 2){

                $total = $monto_alojamiento;
            }
        }


        foreach ($reserva as $reserv) {
            if ($reserv->tipo_moneda_id == 1) {
                $pdf = PDF::loadView('pdf.checkin', ['propiedad' => $propiedad , 'cliente'=> $cliente ,'reservas_pdf'=> $reservas_pdf, 'neto' => $neto , 'iva' => $iva, 'total' => $total]);
                
            }elseif($reserv->tipo_moneda_id == 2){
                $pdf = PDF::loadView('pdf.checkin', ['propiedad' => $propiedad, 'cliente'=> $cliente ,'reservas_pdf'=> $reservas_pdf,'total' => $total]);
                
            }
        }

        return $pdf->download('archivo.pdf');

    }

      public function huesped(Request $request)
      {

            if ($request->has('propiedad_id')) {

              $fecha = Carbon::today()->format('d-m-Y');

              $fecha_actual = Carbon::today()->format('Y-m-d');

                
              $propiedad_id = $request->input('propiedad_id');
              $propiedad = Propiedad::where('id', $request->input('propiedad_id'))->with('pais')->first();

              if (!is_null($propiedad)){

                $habitaciones = Habitacion::where('propiedad_id', $propiedad_id)->whereHas('reservas', function($query){

                            $query->where('estado_reserva_id', 3);

                  })->with(['reservas' => function ($q) use($fecha_actual){

                  $q->where('estado_reserva_id', 3)->where('checkin', '<=', $fecha_actual)->where('checkout', '>', $fecha_actual)->with('huespedes');}])->get();

                  $pdf = PDF::loadView('pdf.huesped', ['propiedad' => [$propiedad], 'fecha' =>  $fecha ,'habitaciones' => $habitaciones]);

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