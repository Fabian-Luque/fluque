<?php

namespace App\Http\Controllers;

use MyAllocator\phpsdk\src\Api\HelloWorld;
use MyAllocator\phpsdk\src\Api\RoomCreate;
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
		$auth->vendorId = 'gofeels';
		$auth->vendorPassword = 'QciEDjWaCFEw';
		$auth->userId = 'gofeels';
  		$auth->userPassword = '78831375';
  		$auth->propertyId = '345';
  		$auth->PMSPropertyId = '';

		$params =array(
            'vendorId' => 'gofeels',
            'vendorPassword' => 'QciEDjWaCFEw',
            'userId' => 'gofeels',
            'userPassword' => '78831375',
            'propertyId' => '555',
            'PMSPropertyId' => '666' 
        );


		





		$api = new RoomCreate();
		$api->setConfig(
			'dataFormat', 
			'array'
		);
		$api->setAuth($auth);

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