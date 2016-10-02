<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use App\User;



class UserController extends Controller
{
    


    public function register(){

    	return view('register');


    }

	public function store(Request $request){

	$rules = array(
			'name' 		=> 	'required',
			'email' 	=> 	'required|unique:users,email',
			'password' 	=>	'required',
			'telefono' 	=>	'required'
			

			);

		

		$validator = Validator::make($request->all(), $rules);


        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator->errors());

        } else {

            
		   $usuario = User::create([
                    'name'     => $request->input('name'),
                    'email'    => $request->input('email'),
                    'password' => bcrypt($request->input('password')),
                    'telefono' => $request->input('telefono'),

                ]);


		return 'usuario creado';

            
        }

		/*$user = User::create($request->all());*/


	}



}
