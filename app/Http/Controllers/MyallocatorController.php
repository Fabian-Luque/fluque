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
            	'm_property_id' => 'required',
            	'prop_id' 		=> 'required',
            	'token_go' 		=> 'required',
           	)
        );

        if ($validator->fails()) {
        	$retorno['errors'] = true;
        	$retorno["msj"] = $validator->errors();
        } else {
        	$cm = CredencialMyallocator::where(
        		'prop_id',
        		$request->prop_id
        	)->first();

        	$client = new Client();

    		try {
	            $response = $client->request(
	            	'POST', 
	            	'https://myallocator.gofeels.com/api/v1/vendor/add_user_account', [
	            		'headers'         => [
	        				'Accept'        => 'application/json',
	        				'Authorization' => 'Bearer '.$request->token_go
	      				],
					    'form_params' => [
					        'user_id' 	=> $request->m_user_id,
					        'user_pass' => $request->m_user_pass
					    ]
					]
				)->getBody();

				$retorno = json_decode($body);
				
	    	} catch (ClientException $e) {
				$retorno = ($e->getResponse()->getBody()->getContents());
	    	}
$retorno["msj"] = $retorno;
/*

        	try {
        		if (isset($cm->prop_id)) {
	        		$cm->m_user_id 	   = $request->m_user_id;
	        		$cm->m_user_pass   = $request->m_user_pass;
	        		$cm->m_user_token  = $request->m_user_token;
	        		$cm->m_property_id = $request->m_property_id;
       				$cm->save();                     

	        		$retorno['errors'] = false;
	    			$retorno['msg'] = "La configuracion de myallocator ha sido creada";
	        	} else {
	        		$cm = new CredencialMyallocator();
	        		$cm->m_user_id 	   = $request->m_user_id;
	        		$cm->m_user_pass   = $request->m_user_pass;
	        		$cm->m_user_token  = $request->m_user_token;
	        		$cm->m_property_id = $request->m_property_id;
                    $cm->save();

	        		$retorno['errors'] = false;
	    			$retorno['msg'] = "La configuracion de myallocator ha sido actualizada";
	        	}
        	} catch (QueryException $e) {
        		$retorno['errors'] = true;
    			$retorno['msg'] = $e->getMessage();
        	}
        	*/		
    	}
    	return Response::json($retorno);
    }
}