<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use App\Propiedad;
use App\Cliente;
use PDF;
use App\Jobs\SendMail;
use Illuminate\Support\Facades\Validator;
use Response;

class Controller extends BaseController {
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    public function SearchDirectory($directorio) { // amazon s3
        $flag = false;
        try {
            if (!empty($directorio)) {
                $directorios = Storage::disk('s3')->allDirectories("");

                for ($i = 0; $i < count($directorios); $i++) { 
                    if (strcmp($directorios[$i], $directorio) == 0) {
                        $flag = true;
                    }

                    if ($flag == true) {
                        break;
                    }
                }
                
                $retorno['error'] = false;
                $retorno['msj'] = 'Delete exitoso';
            } else {
                $retorno['error'] = true;
                $retorno['msj'] = "Datos requeridos";
            }
        } catch (S3Exception $e) {
            $retorno['error'] = true;
            $retorno['msj'] = $e->getMessage();
        } 
        $retorno['existe'] = $flag;
        return $retorno;
    }

    public function EnvioCorreo(Propiedad $propiedad, $cliente_email, $arr, $vista_coreo, $vista_pdf, $nombre_pdf, $opcion, $propiedad_email, $opp) {

        if ($opcion == 0) { // solo descarga
            $pdf = PDF::loadView(
                $vista_pdf, 
                $arr
            );

            return $pdf;
        } elseif ($opcion == 1) { // solo envio de correo
            $job = new SendMail(
                $propiedad,
                $cliente_email,
                $propiedad_email,
                $vista_coreo,
                $vista_pdf,
                $nombre_pdf,
                $arr,
                $opp
            );

            $this->dispatch($job);
            return;
        } else { // ambos!!!!!!!!!!!!!!!!!!!!
            $job = new SendMail(
                $propiedad,
                $cliente_email,
                $propiedad_email,
                $vista_coreo,
                $vista_pdf,
                $nombre_pdf,
                $arr,
                $opp
            );

            $this->dispatch($job);

            $pdf = PDF::loadView(
                $vista_pdf, 
                $arr
            );

            return $pdf;
        } 
    }

    public function getReservasMapa($propiedad_id)
    {
        $propiedad    = Propiedad::where('id', $propiedad_id)->first();
        if (is_null($propiedad)) {
            $retorno = array(
                'msj'    => "Propiedad no encontrada",
                'errors' => true);
            return Response::json($retorno, 404);
        }

        $clientes = Cliente::where(function ($query) use ($propiedad_id) {
            $query->whereHas('reservas.tipoHabitacion', function($query) use($propiedad_id){
                $query->where('propiedad_id', $propiedad_id);
            });
            $query->whereHas('reservas', function($query){
                $query->where('tipo_fuente_id', 9)->where('habitacion_id', null);
            });
        })
        ->with(['reservas' => function ($query) use ($propiedad_id){
                $query->whereHas('tipoHabitacion', function($query) use($propiedad_id){
                        $query->where('propiedad_id', $propiedad_id);
                    })
                    ->where('habitacion_id', null)
                    ->where('tipo_fuente_id', 9)
                    ->whereIn('estado_reserva_id', [1,2,3,4,5])
                    ->orderby('n_reserva_motor')
                    ->with('TipoMoneda')
                    ->with('tipoHabitacion');
            }])
        ->get();

        $data = []; //Arreglo principal
        $aux = 0; //aux de n_reserva_propiedad

        foreach ($clientes as $cliente) {
            $suma_deposito = 0;
            $total    = 0;
            $aux_reservas = []; //Arreglo aux de reserva del mismo cliente y misma operacion desde el motor

                $reservas = $cliente->reservas; 
                $cantidad = count($reservas) - 1;
                foreach ($reservas as $reserva) {

                if ($aux != $reserva->n_reserva_propiedad) {
                    $aux = $reserva->n_reserva_propiedad; //Lo igualo por si existe otra reserva con el mismo n_reserva_propiedad
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

                } elseif($aux == $reserva->n_reserva_propiedad) {

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
                        $aux_reservas = [];
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

        return $data;
    }
}