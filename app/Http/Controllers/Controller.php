<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;

class Controller extends BaseController {
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    public function UploadImage(Request $request) {
        $validator = Validator::make(
        	$request->all(), 
        	array(
            	'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
           	)
        );

        $imageName = time().'.'.$request->image->getClientOriginalExtension();
        $image = $request->file('image');
        $t = Storage::disk('s3')->put(
        	$imageName, 
        	file_get_contents($image), 
        	'public'
        );
        $imageName = Storage::disk('s3')->url($imageName);

    	return Response::json("Upload exitoso");
    }
}
