<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use App\CredencialMyallocator;
use Response;
use \Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Client;

class MyallocatorController extends Controller {
    
    public function Configuracion(Request $request) {
        $validator = Validator::make(
        	$request->all(), 
        	array(
            	'm_user_id'   	=> 'required',
            	'm_user_pass' 	=> 'required',
                'm_user_token'  => 'required',
            	'm_property_id' => 'required',
            	'prop_id' 		=> 'required',
           	)
        );

        if ($validator->fails()) {
        	$retorno['errors'] = true;
        	$retorno['msg'] = $validator->errors();
        } else {
        	$cm = CredencialMyallocator::where(
        		'prop_id',
        		$request->prop_id
        	)->first();

        	try {
        		if (isset($cm->prop_id)) {
	        		$cm->m_user_id 	   = $request->m_user_id;
	        		$cm->m_user_pass   = $request->m_user_pass;
	        		$cm->m_user_token  = $request->m_user_token;
	        		$cm->m_property_id = $request->m_property_id;
       				$cm->save();                     

	        		$retorno['errors'] = false;
	    			$retorno['msg'] = "La configuracion de myallocator ha sido actualizada";
	        	} else {
	        		$cm = new CredencialMyallocator();
	        		$cm->m_user_id 	   = $request->m_user_id;
	        		$cm->m_user_pass   = $request->m_user_pass;
	        		$cm->m_user_token  = $request->m_user_token;
	        		$cm->m_property_id = $request->m_property_id;
                    $cm->prop_id       = $request->prop_id;
                    $cm->save();

	        		$retorno['errors'] = false;
	    			$retorno['msg'] = "La configuracion de myallocator ha sido creada";
	        	}
        	} catch (QueryException $e) {
        		$retorno['errors'] = true;
    			$retorno['msg'] = $e->getMessage();
        	}	
    	}
    	return Response::json($retorno);
    }
}