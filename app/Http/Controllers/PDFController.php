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
use \Carbon\Carbon;
use Response;

class PDFController extends Controller
{
    

	public function estadoCuenta(Request $request){

		$reservas = $request['reservas'];
		$propiedad_id = $request->input('propiedad_id');
		$cliente_id = $request->input('cliente_id');
		$iva = $request->input('iva');



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

		$reserva = Reserva::where('id', $id)->with('cliente.pais', 'cliente.region')->with('habitacion.tipoHabitacion')->with('huespedes.servicios')->get();

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

		
		if($iva == 1){
		$neto = round($monto_alojamiento + $consumo); 
		$iva = round(($neto * $propiedad_iva) / 100);
		$total = round($neto + $iva);
		}elseif($iva == 0){
		$neto = round($monto_alojamiento + $consumo); 
		$iva = round(($neto * 0) / 100);
		$total = round($neto + $iva);

		}

    /*return ['propiedad' => $propiedad,'consumo' => $consumo , 'cliente'=> $cliente ,'reservas_pdf'=> $reservas_pdf, 'neto' => $neto , 'iva' => $iva, 'total' => $total];*/
		$pdf = PDF::loadView('pdf.estado_cuenta', ['propiedad' => $propiedad,'consumo' => $consumo , 'cliente'=> $cliente ,'reservas_pdf'=> $reservas_pdf, 'neto' => $neto , 'iva' => $iva, 'total' => $total]);

		return $pdf->download('archivo.pdf');





	}

  public function huesped(Request $request)
  {

        if ($request->has('propiedad_id')) {

          $fecha = Carbon::today()->format('d-m-Y');

            
          $propiedad_id = $request->input('propiedad_id');
          $propiedad = Propiedad::where('id', $request->input('propiedad_id'))->first();

          if (!is_null($propiedad)){

            $habitaciones = Habitacion::where('propiedad_id', $propiedad_id)->whereHas('reservas', function($query){

                        $query->where('estado_reserva_id', 3);

              })->with(['reservas' => function ($q){

              $q->where('estado_reserva_id', 3)->with('huespedes');}])->get();


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


	public function reporteDiario(Request $request)
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

                                          $ingresos_por_habitacion += number_format($monto_pagado_habitacion / $conversion, 2,'.','');
                                          $ingresos_por_consumos += number_format($monto_pagado_consumos / $conversion, 2, '.', '');
                                        
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






                }//FIN IF

                $ingresos = array($ingresos_hab);


                $pdf = PDF::loadView('pdf.reporte_diario', ['propiedad' => [$propiedad], 'reservas_realizadas'=> count($reservas),'reservas_anuladas' => count($reservas_anuladas), 'reservas_no_show' => count($reservas_no_show), 'fecha' => $fecha1, 'ingresos_habitacion' => $ingresos_habitacion, 'ingresos_consumo' => $ingresos_consumos, 'ingresos_totales' => $ingresos_totales_dia, 'ingresos_efectivo' => $ingresos_por_efectivo, 'ingresos_credito' => $ingresos_por_credito, 'ingresos_debito' => $ingresos_por_debito, 'ingresos_cheque' => $ingresos_por_cheque, 'ingresos_tarjeta_credito' => $ingresos_por_tarjeta_credito, 'ingresos_transferencia' => $ingresos_por_transferencia, 'pagina_web' => $pagina_web, 'caminando' => $caminando, 'telefono' => $telefono, 'email' => $email, 'redes_sociales' => $redes_sociales, 'expedia' => $expedia, 'booking' => $booking, 'airbnb' => $airbnb, 'ingresos_particular' => $ingresos_por_particulares, 'ingresos_empresa' => $ingresos_por_empresas]);


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