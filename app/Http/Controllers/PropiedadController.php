<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use App\Propiedad;
use Response;
use App\TipoPropiedad;
use App\habitacion;
use App\TipoHabitacion;
use App\Servicio;







class PropiedadController extends Controller
{




    public function ingresoServicio(Request $request){
   

       if($request->has('venta_servicio') && $request->has('propiedad_id') && $request->has('metodo_pago_id')){

   
         $propiedad =  Propiedad::where('id', $request->input('propiedad_id'))->first();
         $metodo_pago_id = $request->input('metodo_pago_id');

          if(!is_null($propiedad)){



           $servicios = $request->input('venta_servicio');

            foreach ($servicios as $servicio) {
               
                
                $servicio_id = $servicio['servicio_id'];
                $cantidad = $servicio['cantidad'];
                $precio_total = $servicio['precio_total'];
                


                 $serv = Servicio::where('id', $servicio_id)->where('propiedad_id', $request->input('propiedad_id'))->first();

                 if(!is_null($serv)){
                 $servicio_id = $serv->id;
                 $servicio_nombre = $serv->nombre;


                $propiedad->vendeServicios()->attach($servicio_id, ['metodo_pago_id' => $metodo_pago_id,'cantidad' => $cantidad , 'precio_total' => $precio_total]);
              


                 }else{

                $retorno = array(

                'msj'    => "El servicio no pertenece a la propiedad",
                'errors' => true
                );

                return Response::json($retorno, 400);





             }

                
            }

                $retorno = array(

                    'msj' => "Servicios ingresados correctamente",
                    'erros' =>false
                );

                return Response::json($retorno, 200);
          

          }else{



                $data = array(

                    'msj' => "Propiedad no encontrada",
                    'errors' => true


                );

            return Response::json($data, 404);



          }


       }else{


            $retorno = array(

                'msj'    => "La solicitud esta incompleta",
                'errors' => true
            );

            return Response::json($retorno, 400);



       }




    }



    public function index(Request $request){

        if($request->has('id')){
            $propiedad = Propiedad::where('id', $request->input('id'))->with('tipoPropiedad')->get();
            return $propiedad;

        }




    }


		public function show($id){

		  try {



           $propiedad = Propiedad::where('id', $id)->get();

           $tipos = TipoHabitacion::whereHas('habitaciones', function($query) use($id){

                    $query->where('propiedad_id', $id);

           })->get();

           foreach ($propiedad as $prop) {

                $prop->tipos_habitaciones = count($tipos);


           }

           return $propiedad;





        } catch (ModelNotFoundException $e) {
            $data = [
                'errors' => true,
                'msg'    => $e->getMessage(),
            ];
            return Response::json($data, 404);
        }


	}




    

	public function update(Request $request, $id){


		$rules = array(

			'nombre' 						=> '',
			'tipo_propiedad_id'	 			=> 'numeric',
			'numero_habitaciones'			=> 'numeric',
			'pais' 							=> '',
			'ciudad'					    => '', 
			'region'					    => '',
			'direccion'					    => '',
			'telefono'					    => '',
			'email'					        => '',
			'nombre_responsable'	        => '',
			'descripcion'					=> '',
            'iva'                           => 'numeric',
            'porcentaje_deposito'           => 'numeric',

			
		);

 	$validator = Validator::make($request->all(), $rules);


 	     if ($validator->fails()) {

            $data = [

                'errors' => true,
                'msg' => $validator->messages(),

            ];

            return Response::json($data, 400);

        } else {

            $propiedad = Propiedad::findOrFail($id);
            $propiedad->update($request->all());
            $propiedad->touch();

            $data = [

                'errors' => false,
                'msg' => 'Propiedad actualizada satisfactoriamente',

            ];

            return Response::json($data, 201);

        }



	}


    public function getTipoPropiedad(){

    $TipoPropiedad = TipoPropiedad::all();
        return $TipoPropiedad; 
    }


}
