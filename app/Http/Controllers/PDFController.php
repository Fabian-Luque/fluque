<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use PDF;
use App\Reserva;

class PDFController extends Controller
{
    

	public function getPDF(){


		$reservas = Reserva::where('id' , 1)->get();

		$pdf = PDF::loadView('pdf.vista', ['reservas' => $reservas]);
		return $pdf->stream('archivo.pdf');





	}




}
