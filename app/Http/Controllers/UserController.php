<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use App\User;
use Response;
use App\Propiedad;
use DB;




class UserController extends Controller
{


	public function index(){

		$usuarios = User::all();


	      $respuesta = [

            'data' => $usuarios,
            'errors' => false,

        ];

        return Response::json($respuesta, 200);

  

	}
    


    public function register(){

    	return view('register');


    }

	public function store(Request $request){

	$rules = array(
			'name' 					=> 	'required',
			'email' 				=> 	'required|unique:users,email',
			'phone' 				=>	'required',
			'password' 				=>	'required',
			'nombre'				=>  'required',
			'tipo'					=>	'required',
			'numero_habitaciones'   =>	'required',
			'ciudad'				=>  'required',
			'direccion'				=> 	'required',

			
			);


		$validator = Validator::make($request->all(), $rules);


        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator->errors());

        } else {

            
      	    $usuario                       = new User();
            $usuario->name          	   = $request->get('name');
           	$usuario->email            	   = $request->get('email');
          	$usuario->password             = bcrypt($request->get('password'));
          	$usuario->phone                = $request->get('phone');
   
            $usuario->save();


            $propiedad                    	 = new Propiedad();
			$propiedad->nombre               = $request->get('nombre');
			$propiedad->tipo              	 = $request->get('tipo');
			$propiedad->numero_habitaciones  = $request->get('numero_habitaciones');
			$propiedad->ciudad               = $request->get('ciudad');
			$propiedad->direccion            = $request->get('direccion');
			$propiedad->user_id				 = $usuario->id; 

 			$propiedad->save();
            

			return 'usuario creado';

            
        }


	}



}
