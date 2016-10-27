<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use App\User;
use Response;
use App\Propiedad;
use App\Servicio;
use DB;




class UserController extends Controller
{



/*	public function index(){

		$usuarios = User::all();


	      $respuesta = [

            'data' => $usuarios,
            'errors' => false,

        ];

        return Response::json($respuesta, 200);

  

	}*/


	public function show($id){

		  try {
            return User::where('id', $id)->with('propiedad')->get();
        } catch (ModelNotFoundException $e) {
            $data = [
                'errors' => true,
                'msg'    => $e->getMessage(),
            ];
            return Response::json($data, 404);
        }





	}
    


	public function store(Request $request){

	$rules = array(
			'name' 					=> 	'required',
			'email' 				=> 	'required|unique:users,email',
			'phone' 				=>	'required',
			'password' 				=>	'required|min:6',
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
          	/*$usuario->password             = bcrypt($request->get('password'));*/
          	$usuario->password             = $request->get('password');
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


            $servicio1                        = new Servicio();
            $servicio1->nombre                = 'Desayuno';
            $servicio1->categoria             = '';
            $servicio1->precio                = '';
            $servicio1->propiedad_id          = $usuario->id; 

            $servicio1->save();

            $servicio2                        = new Servicio();
            $servicio2->nombre                = 'Almuerzo';
            $servicio2->categoria             = '';
            $servicio2->precio                = '';
            $servicio2->propiedad_id          = $usuario->id; 

            $servicio2->save();

            $servicio3                        = new Servicio();
            $servicio3->nombre                = 'Cena';
            $servicio3->categoria             = '';
            $servicio3->precio                = '';
            $servicio3->propiedad_id          = $usuario->id; 

            $servicio3->save();








            

			/*return 'usuario creado';*/

			      $data = [
                'errors' => false,
                'msg' => 'usuario creado satisfactoriamente',

            	];

			return Response::json($data, 201);

            


        }


	}


	public function update(Request $request, $id){

			$rules = array(

			'name' 				=> 'required',
			'email'	 			=> 'email|required',
			'password'			=> 'min:6',
			'phone'				=> 'required',

			
		);

		$validator = Validator::make($request->all(), $rules);


 	     if ($validator->fails()) {

            $data = [

                'errors' => true,
                'msg' => $validator->messages(),

            ];

            return Response::json($data, 400);

        } else {

            $user = User::findOrFail($id);
            $user->update($request->all());
            $user->touch();

            $data = [

                'errors' => false,
                'msg' => 'Usuario actualizado satisfactoriamente',

            ];

            return Response::json($data, 201);

        }






	}






}
