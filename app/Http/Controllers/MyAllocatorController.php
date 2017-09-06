<?php

namespace App\Http\Controllers;

use MyAllocator\phpsdk\src\Api\HelloWorld;
use MyAllocator\phpsdk\src\Api\HelloVendor;
use MyAllocator\phpsdk\src\Object\Auth;

use Illuminate\Http\Request;
use App\Http\Requests;
use Response;

class MyAllocatorController extends Controller {
/*
MYALLOCATOR_VENDOR_ID
MYALLOCATOR__VENDOR_PASS
*/	

	public function ejm(Request $Request) {
		$auth = new Auth();

		$params = array(
    		'bjhbjh' => '',
    		'hello' => 'world'
		);

		$auth = array(
    		'Auth' => 'true',
    		'hello' => 'world'
		);

		$api = new HelloWorld();
		$api->setConfig(
			'dataFormat', 
			'array'
		);

/*
		$auth->vendorId 	  = env('MYALLOCATOR_VENDOR_ID');
		$auth->vendorPassword = env('MYALLOCATOR__VENDOR_PASS');
*/		
	
		
		try {
    		$rsp = $api->callApiWithParams($params);
		} catch (Exception $e) {
    		$rsp = 'Oops: '.$e->getMessage();
		}

		return Response::json($rsp); 
	}
}