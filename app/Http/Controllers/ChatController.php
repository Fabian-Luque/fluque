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

class ChatController extends Controller {
    
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

            Event::fire(
                new ChatEvent($mensaje->receptor_id)
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
            $msj_emisor = Mensajeria::where(
                'emisor_id',
                $request->receptor_id
            )->orderBy('created_at', 'desc')
             ->orderBy('emisor_id')
             ->take(7)
            ->get();

            $recep_ids = $msj_emisor->lists('receptor_id');
            $mensajes = collect([]);

            foreach ($recep_ids as $id) {
                $mensajes->push(
                    $this->GetConv(
                        $id, 
                        $request->receptor_id
                    )
                );
            }

            $retorno['errors'] = false;
            $retorno["msj"] = $mensajes;
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