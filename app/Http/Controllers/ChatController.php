<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Response;
use App\Mensajeria;   
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller {
    public function SendMessage(Request $request) {
    	$validator = Validator::make(
        	$request->all(), 
        	array(
            	'emisor_id'   => 'required',
            	'receptor_id' => 'required',
            	'mensaje' 	  => 'required',
           	)
        );

        if ($validator->fails()) {
        	$retorno['errors'] = true;
        	$retorno["msj"] = $validator->errors();
        } else {
        	$mensaje 			  = new Mensajeria();
        	$mensaje->emisor_id   = $request->emisor_id;
        	$mensaje->receptor_id = $request->receptor_id;
        	$mensaje->mensaje 	  = $request->mensaje;
        	$mensaje->save();

        	$retorno['errors'] = true;
        	$retorno["msj"] = "mensaje enviado correctamente"
        }
    	return Response::json($retorno);
    }
}