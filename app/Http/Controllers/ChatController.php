<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Response;
use App\Mensajeria;   
use Illuminate\Support\Facades\Validator;
use App\Events\ChatEvent;
use Illuminate\Support\Facades\Event;
use \Carbon\Carbon;

class ChatController extends Controller {

    public function ConvNoLeidas(Request $request){
        $validator = Validator::make(
            $request->all(), 
            array(
                'receptor_id' => 'required'
            )
        );

        if ($validator->fails()) {
            $retorno['errors'] = true;
            $retorno["msj"] = $validator->errors();
        } else {
            $conv_no_leidas = Mensajeria::where(
                'receptor_id',
                $request->receptor_id
            )->where(
                'estado',
                0
            )->get();

            $retorno['errors'] = false;
            $retorno["msj"] = $conv_no_leidas->count();
        }
        return Response::json($retorno);
    }
    
    public function SendMessage(Request $request) {
        $validator = Validator::make(
            $request->all(), 
            array(
                'emisor_id'   => 'required',
                'receptor_id' => 'required',
                'mensaje'     => 'required'
            )
        );

        if ($validator->fails()) {
            $retorno['errors'] = true;
            $retorno["msj"] = $validator->errors();
        } else {
            $mensaje              = new Mensajeria();
            $mensaje->emisor_id   = $request->emisor_id;
            $mensaje->receptor_id = $request->receptor_id;
            $mensaje->mensaje     = $request->mensaje;
            $mensaje->save();

            $conv_no_leidas = Mensajeria::where(
                'receptor_id',
                $request->receptor_id
            )->where(
                'estado',
                0
            )->get();

            Event::fire(
                new ChatEvent(
                    $mensaje->receptor_id,
                    $conv_no_leidas->count()
                )
            );

            $retorno['errors'] = false;
            $retorno["msj"] = "mensaje enviado correctamente ". $mensaje;
        }
        return Response::json($retorno);
    }

    public function GetAllMessages(Request $request) {
        if ($request->has('emisor_id') && $request->has('receptor_id')) {
            $mensajes = Mensajeria::where(
                'emisor_id',
                $request->emisor_id
            )->where(
                'receptor_id',
                $request->receptor_id
            )->get();
            
            $retorno['errors'] = false;
            $retorno["msj"] = $mensajes;
        } elseif ($request->has('receptor_id')) {
            $mensajes = Mensajeria::where(
                'receptor_id',
                $request->receptor_id
            )->get();
            
            $retorno['errors'] = false;
            $retorno["msj"] = $mensajes;
        } else {
            $retorno['errors'] = true;
            $retorno["msj"] = "Datos requeridos";
        }
        return Response::json($retorno);
    }

    public function GetMessagesByReceptor(Request $request) {
        if ($request->has('receptor_id')) {

            $receptor_id = $request->receptor_id;

            $msj_emisor = Mensajeria::where(
                'emisor_id',
                $request->receptor_id
            )
             ->orderBy('created_at', 'desc')
             ->orderBy('emisor_id')
             ->get();

            $msj_receptor = Mensajeria::where(
                'receptor_id',
                $request->receptor_id
            )
            ->orderBy('created_at', 'desc')
             ->orderBy('emisor_id')
            ->get();

            $ids1 = $msj_emisor->pluck('receptor_id')->all();
            $ids2 = $msj_receptor->pluck('emisor_id')->all();
            $ids  = array_values(
                array_unique(
                    array_merge(
                        $ids1, 
                        $ids2
                    )
                )
            );

            $ultimos = collect([]);

            for ($i = 0; $i < count($ids); $i++) {
                $ultimos->push(
                    $this->GetConv(
                        $ids[$i], 
                        $request->receptor_id
                    )
                ); 
            }
            // ordenar colecion 
            $flag = true;   
            $temp;  

            while ($flag) {
                $flag = false;    
                for($j = 0; $j < ($ultimos->count() - 1); $j++) {
                    $aux1 = Carbon::parse($ultimos[$j]->updated_at);
                    $aux2 = Carbon::parse($ultimos[$j + 1]->updated_at);
                    
                    if (!$aux1->gt($aux2)) {
                        $temp = $ultimos[$j];                
                        $ultimos[$j] = $ultimos[$j + 1];
                        $ultimos[$j + 1] = $temp;
                        $flag = true;          
                    } 
                }
            }
            // return $ultimos;
            foreach ($ultimos as $ultimo) {
                if ($ultimo->propiedad_emisor->id != $receptor_id) {
                    $ultimo->chat_nombre       = $ultimo->propiedad_emisor->nombre;
                    $ultimo->chat_id = $ultimo->propiedad_emisor->id;
                } 

                if ($ultimo->propiedad_receptor->id != $receptor_id) {
                    $ultimo->chat_nombre = $ultimo->propiedad_receptor->nombre;
                    $ultimo->chat_id = $ultimo->propiedad_receptor->id;
                }
            }

            $retorno['errors'] = false;
            $retorno["msj"] = $ultimos;
        } else {
            $retorno['errors'] = true;
            $retorno["msj"] = "Datos requeridos";
        }
        return Response::json($retorno);
    }

    public function GetConv($emisor_id, $receptor_id) {
        $mensajes = Mensajeria::whereIn(
            'emisor_id',
            [$emisor_id, $receptor_id])
        ->whereIn('receptor_id', [$emisor_id, $receptor_id])
        ->with('propiedad_emisor', 'propiedad_receptor')
        ->orderBy('created_at', 'DESC')
        ->take(1)
        ->get();
        return $mensajes[0];
    }

    public function GetConversacion(Request $request) {
        $validator = Validator::make(
            $request->all(), 
            array(
                'emisor_id'   => 'required',
                'receptor_id' => 'required',
                'limit'       => 'required',
                'prop_id'     => 'required'
            )
        );

        if ($validator->fails()) {
            $retorno['errors'] = true;
            $retorno["msj"] = $validator->errors();
        } else {

            $emisor_id   = $request->emisor_id;
            $receptor_id = $request->receptor_id;

            $mensajes = Mensajeria::whereIn(
                'emisor_id',
                [$emisor_id, $receptor_id])
            ->with('propiedad_emisor', 'propiedad_receptor')
            ->whereIn('receptor_id', [$emisor_id, $receptor_id])
            ->orderBy('created_at', 'DESC')
            ->take($request->limit)
            ->get();

            foreach ($mensajes as $mensaje) {
                if ($mensaje->estado == 0 && $mensaje->receptor_id == $request->prop_id) {
                    $mensaje->update([
                        'estado' => 1
                    ]);
                }
            }
            
            $retorno['errors'] = false;
            $retorno["msj"] = $mensajes;
        } 
        return Response::json($retorno);
    }

    public function EstadoMensaje(Request $request) {
        $validator = Validator::make(
            $request->all(), 
            array(
                'mensaje_id'   => 'required'
            )
        );

        if ($validator->fails()) {
            $retorno['errors'] = true;
            $retorno["msj"] = $validator->errors();
        } else {
            $mensaje = Mensajeria::find($request->mensaje_id);

            if (!is_null($mensaje)) {
                $mensaje->update([
                    'estado' => 1
                ]);

                $retorno['errors'] = false;
                $retorno["msj"] = "El mensaje ha sido visto";
            } else {
                $retorno['errors'] = true;
                $retorno["msj"] = "Mensaje no encontrado";
            }
        } 
        return Response::json($retorno);
    }
}