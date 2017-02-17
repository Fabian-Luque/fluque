<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use PDF;
use App\Reserva;
use App\Propiedad;
use App\Cliente;
use \Carbon\Carbon;

class PDFController extends Controller
{
    

	public function getPDF(Request $request){

		$reservas = $request['reservas'];
		$propiedad_id = $request->input('propiedad_id');
		$cliente_id = $request->input('cliente_id');
		$iva = $request->input('iva');



		$propiedad = Propiedad::where('id', $propiedad_id)->get();
		$cliente = Cliente::where('id', $cliente_id)->get();

		$propiedad_iva = 0;
		foreach ($propiedad as $prop) {
			
			$propiedad_iva = $prop->iva;

		}

		$reservas_pdf = [];
		$monto_alojamiento = 0;
		$consumo = 0;
		foreach($reservas as $id){

		$reserva = Reserva::where('id', $id)->with('cliente')->with('habitacion.tipoHabitacion')->with(['huespedes.servicios' => function ($q) {

        $q->where('estado', 'Por pagar');}])->get();

			foreach ($reserva as $ra) {
				$monto_alojamiento += $ra->monto_alojamiento;
				foreach($ra->huespedes as $huesped){
					$huesped->monto_consumo = 0;
					foreach($huesped->servicios as $servicio){
					/*	return $servicio;*/
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


		$pdf = PDF::loadView('pdf.vista', ['propiedad' => $propiedad,'consumo' => $consumo , 'cliente'=> $cliente ,'reservas_pdf'=> $reservas_pdf, 'neto' => $neto , 'iva' => $iva, 'total' => $total]);

		return $pdf->download('archivo.pdf');





	}




}