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
use App\TipoHabitacion;
use DB;




class UserController extends Controller
{




	public function show($id){

		  try {

            $users = User::where('id', $id)->with('propiedad')->get();

            foreach ($users as $user) {
                    
                $id = $user->propiedad->id;

                $tipos = TipoHabitacion::whereHas('habitaciones', function($query) use($id){

                    $query->where('propiedad_id', $id);

                })->get();


                $user->tipos_habitaciones = count($tipos);

            }

            return $users;

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
			'tipo_propiedad_id'		=>	'required|numeric',
			'numero_habitaciones'   =>	'required|numeric',
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
          	/*$usuario->password             = $request->get('password');*/
          	$usuario->phone                = $request->get('phone');
   
            $usuario->save();


            $propiedad                    	 = new Propiedad();
            $propiedad->id                   = $usuario->id;
			$propiedad->nombre               = $request->get('nombre');
			$propiedad->numero_habitaciones  = $request->get('numero_habitaciones');
			$propiedad->ciudad               = $request->get('ciudad');
			$propiedad->direccion            = $request->get('direccion');
            $propiedad->tipo_propiedad_id    = $request->get('tipo_propiedad_id');
			$propiedad->user_id				 = $usuario->id; 

 			$propiedad->save();


            $servicio1                        = new Servicio();
            $servicio1->nombre                = 'Desayuno';
            $servicio1->precio                = '';
            $servicio1->cantidad_disponible   = 0;
            $servicio1->categoria_id          = 1;
            $servicio1->propiedad_id          = $usuario->id; 

            $servicio1->save();

            $servicio2                        = new Servicio();
            $servicio2->nombre                = 'Almuerzo';
            $servicio2->precio                = '';
            $servicio2->cantidad_disponible   = 0;
            $servicio2->categoria_id          = 1;
            $servicio2->propiedad_id          = $usuario->id; 

            $servicio2->save();

            $servicio3                        = new Servicio();
            $servicio3->nombre                = 'Cena';
            $servicio3->precio                = '';
            $servicio3->cantidad_disponible   = 0;
            $servicio3->categoria_id          = 1;
            $servicio3->propiedad_id          = $usuario->id; 

            $servicio3->save();



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
