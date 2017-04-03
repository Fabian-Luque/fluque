<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use Response;
use App\Servicio;
use App\Propiedad;
use App\Categoria;
use App\PrecioServicio;

class ServicioController extends Controller
{


    ///////////////prueba////////////////////////

    
		public function index(Request $request){

    	  if($request->has('propiedad_id')){
            return $servicios = Propiedad::where('id', $request->input('propiedad_id'))->with('servicios.precios')->get();


        }
        
    }



		public function store(Request $request){

			$rules = array(

			'nombre' 		      => 'required',
            'precios'             => 'required|array',
            'cantidad_disponible' => 'numeric',
			'categoria_id'	      => 'required|numeric',
			'propiedad_id'        => 'required|numeric',


			
		);

			$validator = Validator::make($request->all(), $rules);


 	     if ($validator->fails()) {

            $data = [

                'errors' => true,
                'msg' => $validator->messages(),

            ];

            return Response::json($data, 400);

        } else {


            $servicio                             = new Servicio();
            $servicio->nombre          	          = $request->get('nombre');
            $servicio->cantidad_disponible        = $request->get('cantidad_disponible');
           	$servicio->categoria_id           	  = $request->get('categoria_id');
          	$servicio->propiedad_id               = $request->get('propiedad_id');
   
            $servicio->save();


            foreach ($request->get('precios') as $precio) {
            
                $precio_servicio = $precio['precio_servicio'];
                $tipo_moneda_id = $precio['tipo_moneda_id'];

                $precio                           = new PrecioServicio();
                $precio->precio_servicio          = $precio_servicio;
                $precio->tipo_moneda_id           = $tipo_moneda_id;
                $precio->servicio_id              = $servicio->id;
                $precio->save();
               
                
            }
            

			     $data = [
                'errors' => false,
                'msg' => 'Servicio creado satisfactoriamente',

            	];

			return Response::json($data, 201);

        }



	}  


	    public function update(Request $request, $id){

			$rules = array(

			'nombre' 		       => '',
			'precios'              => 'array',
            'cantidad_disponible'  => 'numeric',
            'categoria_id'         => 'numeric',


			
		);

			$validator = Validator::make($request->all(), $rules);


 	     if ($validator->fails()) {

            $data = [

                'errors' => true,
                'msg' => $validator->messages(),

            ];

            return Response::json($data, 400);

        } else {


            $servicio = Servicio::findOrFail($id);

            $servicio->update($request->all());
            $servicio->touch();

            foreach ($request->get('precios') as $precio) {
                
                $id = $precio['id'];
                $precio_servicio = $precio['precio_servicio'];

                $precio = PrecioServicio::where('id', $id)->first();
                $precio->update(array('precio_servicio' => $precio_servicio));

            }



            $data = [

                'errors' => false,
                'msg' => 'Servicio actualizado satisfactoriamente',

            ];

            return Response::json($data, 201);

        }



	}  


        public function crearPrecio(Request $request){

                $servicio_id  =  $request->input('servicio_id');

                $tipo_moneda_id =  $request->input('tipo_moneda_id');

                $precio_servicio = $request->input('precio_servicio');

                $precio                           = new PrecioServicio();
                $precio->precio_servicio          = $precio_servicio;
                $precio->tipo_moneda_id           = $tipo_moneda_id;
                $precio->servicio_id              = $servicio_id;
                $precio->save();

                $data = [
                'errors' => false,
                'msg' => 'Precio creado satisfactoriamente',

                ];

            return Response::json($data, 201);





         }





	    public function destroy($id){

        $servicio = Servicio::findOrFail($id);
        $servicio->delete();

        $data = [

            'errors' => false,
            'msg'    => 'Servicio eliminado satisfactoriamente',

        ];

        return Response::json($data, 202);



    }


    public function getCategoria(){

        $categorias = Categoria::all();

        return $categorias;



    }


    public function copiaPrecios(){



         $servicios = Servicio::all();

          foreach ($servicios as $servicio) {
             
             $precio_servicio = $servicio->precio;
             $servicio_id = $servicio->id;

             $precio                           = new PrecioServicio();
             $precio->precio_servicio          = $precio_servicio;
             $precio->tipo_moneda_id           = 1;
             $precio->servicio_id              = $servicio_id;
             $precio->save();



          }

             $data = [
                'errors' => false,
                'msg' => 'Precios creados satisfactoriamente',

                ];

            return Response::json($data, 201);




         }








}
