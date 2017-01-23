<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use PDF;
use App\Reserva;
use App\Propiedad;
use App\Cliente;

class PDFController extends Controller
{
    

	public function getPDF(Request $request){

		$reservas = $request['reservas'];
		$propiedad_id = $request->input('propiedad_id');
		$cliente_id = $request->input('cliente_id');



		$propiedad = Propiedad::where('id', $propiedad_id)->get();
		$cliente = Cliente::where('id', $cliente_id)->get();



		$reservas_pdf = [];
		$suma_total = 0;
		foreach($reservas as $id){

		$reserva = Reserva::where('id', $id)->with('cliente')->with('habitacion')->get();

			foreach ($reserva as $ra) {
				$suma_total += $ra->monto_total;

			}

		array_push($reservas_pdf, $reserva);


		}

		/*return $reservas_pdf;*/

		$neto = $suma_total;
		$iva = ($suma_total * 19) / 100;
		$total = $neto + $iva;

	


		$pdf = PDF::loadView('pdf.vista', ['propiedad' => $propiedad,  'cliente'=> $cliente ,'reservas_pdf'=> $reservas_pdf, 'neto' => $neto , 'iva' => $iva, 'total' => $total]);
		return $pdf->stream('archivo.pdf');





	}




}
