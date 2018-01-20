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

            $mensajes = Mensajeria::where(
                'receptor_id',
                $request->receptor_id
            )->orderBy('created_at', 'desc')
             ->orderBy('emisor_id', 'asc')
            ->get();

            $aux = collect([]);
            $emisores = $mensajes->lists('emisor_id');
            $len = $mensajes->count();

            if ($len != 0) {
                for ($i = 1; $i < $mensajes->count(); $i++) { 
                    if ($mensajes[($i - 1)]->emisor_id != $mensajes[$i]->emisor_id) {
                        $aux->push($mensajes[($i-1)]);
                    } 
                }
                $aux->push($mensajes[($len - 1)]);
            } else {
                $aux->push($mensajes[($len - 1)]);
            }

            $retorno['errors'] = false;
            $retorno["msj"] = $aux->all();
        } else {
            $retorno['errors'] = true;
            $retorno["msj"] = "Datos requeridos";
        }
        return Response::json($retorno);
    }

    public function GetConversacion(Request $request) {
        $validator = Validator::make(
            $request->all(), 
            array(
                'emisor_id'   => 'required',
                'receptor_id' => 'required',
                'limit'       => 'required'
            )
        );

        if ($validator->fails()) {
            $retorno['errors'] = true;
            $retorno["msj"] = $validator->errors();
        } else {
            $mensajes = Mensajeria::where(
                'emisor_id',
                $request->emisor_id
            )->where(
                'receptor_id',
                $request->receptor_id
            )->orWhere(
                'receptor_id',
                $request->emisor_id
            )->orWhere(
                'emisor_id',
                $request->receptor_id
            )->orderBy('created_at', 'desc')
            ->take($request->limit)->get();
            
            $retorno['errors'] = false;
            $retorno["msj"] = $mensajes;
        } 
        return Response::json($retorno);
    }
}