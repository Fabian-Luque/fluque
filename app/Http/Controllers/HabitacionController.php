<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use Response;
use App\Habitacion;
use App\Equipamiento;
use App\Propiedad;
use App\Reserva;
use Carbon\Carbon;
use App\TipoHabitacion;
use App\TipoMoneda;
use App\Precio;





class HabitacionController extends Controller
{


    public function disponibilidad(Request $request){

/*        $rango = [$fecha_inicio, $fecha_fin];

        $habitaciones = Habitacion::where('propiedad_id', $request->input('propiedad_id'))->whereHas('reservas', function($query) use($fecha_inicio,$fecha_fin){

                    $query->Where('checkin', '>' ,$fecha_inicio)->Where('checkin', '>=', $fecha_fin);

        })->get();


        return $habitaciones;*/


        $propiedad_id = $request->input('propiedad_id');
        $fecha_inicio = $request->input('fecha_inicio');
        $fecha_fin = $request->input('fecha_fin');


        $propiedad = Propiedad::where('id', $propiedad_id)->first();

        if(!is_null($propiedad)){

        $fechaInicio=strtotime($fecha_inicio);
        $fechaFin=strtotime($fecha_fin);

        if($fechaInicio < $fechaFin){


        $habitaciones_ocupadas = [];
        $all_habitaciones = [];
        $habitaciones_disponibles = [];




        $habitaciones_propiedad = Habitacion::where('propiedad_id', $propiedad_id)->with('precios.TipoMoneda')->get();


    
        for($i=$fechaInicio; $i<$fechaFin; $i+=86400){
            
            $fecha = date("Y-m-d", $i);


            $habitaciones = Habitacion::where('propiedad_id', $request->input('propiedad_id'))->whereHas('reservas', function($query) use($fecha){

                    $query->where('checkin','<=' ,$fecha)->where('checkout', '>', $fecha);

        })->with('precios.TipoMoneda')->get();


           foreach ($habitaciones as $habitacion){

                if(!in_array($habitacion, $habitaciones_ocupadas)){


                    array_push($habitaciones_ocupadas, $habitacion);
            
                }
           }
        }

         foreach ($habitaciones_propiedad as $hab) {

            array_push($all_habitaciones, $hab);



         }


         foreach ($all_habitaciones as $hab){

                if(!in_array($hab, $habitaciones_ocupadas)){

                array_push($habitaciones_disponibles, $hab);

            }


        }


        $habitacion_individual          = [];
        $habitacion_doble               = [];
        $habitacion_triple              = [];
        $habitacion_cuadruple           = [];
        $habitacion_quintuple           = [];
        $habitacion_matrimonial         = [];
        $habitacion_suite               = [];
        $habitacion_presidencial        = [];

        foreach ($habitaciones_disponibles as $habitacion) {
            
            if($habitacion->tipo_habitacion_id == 1){

                array_push($habitacion_individual, $habitacion);

            }elseif($habitacion->tipo_habitacion_id == 2){

                array_push($habitacion_doble, $habitacion);


            }elseif($habitacion->tipo_habitacion_id == 3){


                array_push($habitacion_triple, $habitacion);

            }elseif ($habitacion->tipo_habitacion_id == 4) {

                array_push($habitacion_cuadruple, $habitacion);
                
            }elseif ($habitacion->tipo_habitacion_id == 5) {

                array_push($habitacion_quintuple, $habitacion);

            }elseif ($habitacion->tipo_habitacion_id == 6) {

                array_push($habitacion_matrimonial, $habitacion);

            }elseif ($habitacion->tipo_habitacion_id == 7) {

                array_push($habitacion_suite, $habitacion);

            }elseif ($habitacion->tipo_habitacion_id == 8) {

                array_push($habitacion_presidencial, $habitacion);
            }

        }

    $habitaciones_tipo = array(
            'tipos'           => [
        ['id' => 1, 'nombre' => 'individual',   'habitaciones' => $habitacion_individual    ],
        ['id' => 2, 'nombre' => 'doble',        'habitaciones' => $habitacion_doble         ],
        ['id' => 3, 'nombre' => 'triple',       'habitaciones' => $habitacion_triple        ],
        ['id' => 4, 'nombre' => 'cuadruple',    'habitaciones' => $habitacion_cuadruple     ],
        ['id' => 5, 'nombre' => 'quintuple',    'habitaciones' => $habitacion_quintuple     ],
        ['id' => 6, 'nombre' => 'matrimonial',  'habitaciones' => $habitacion_matrimonial   ],
        ['id' => 7, 'nombre' => 'suite',        'habitaciones' => $habitacion_suite         ],
        ['id' => 8, 'nombre' => 'presidencial', 'habitaciones' => $habitacion_presidencial  ],


    ],

    );


        return $habitaciones_tipo;




        }else{

            $retorno = array(

                'msj'    => "Las fechas no corresponden",
                'errors' => true
            );

            return Response::json($retorno, 400);



        }

        

        }else{

            
            $data = array(

                    'msj' => "Propiedad no encontrada",
                    'errors' => true

                );

            return Response::json($data, 404);



        }


       


    }



    /**
     * se obtiene las habitaciones disponibles en un rango de fechas
     *
     * @author ALLEN
     *
     * @param  Request          $request (propiedad_id, fecha_inicio, fecha_fin)
     * @return Response::json
     */

/*    public function Disponibilidad(Request $request){

        $fecha_inicio = $request->input('fecha_inicio');
        $fecha_fin    = $request->input('fecha_fin');
            

        $rango = [$fecha_inicio, $fecha_fin];

        $dias = ((strtotime($fecha_fin)-strtotime($fecha_inicio))/86400)+1;



        if($request->has('propiedad_id')){

         $habitaciones = Habitacion::where('propiedad_id', $request->input('propiedad_id'))->whereHas('calendarios', function($query) use($rango) {
           $query->whereBetween('fecha',  $rango)->where('reservas', 0);}, '=', $dias)->with('tipoHabitacion')->get();


    }


        $habitacion_individual          = [];
        $habitacion_doble               = [];
        $habitacion_triple              = [];
        $habitacion_cuadruple           = [];
        $habitacion_quintuple           = [];
        $habitacion_matrimonial         = [];
        $habitacion_suite               = [];
        $habitacion_presidencial        = [];

        foreach ($habitaciones as $habitacion) {
            
            if($habitacion->tipo_habitacion_id == 1){

                array_push($habitacion_individual, $habitacion);

            }elseif($habitacion->tipo_habitacion_id == 2){

                array_push($habitacion_doble, $habitacion);


            }elseif($habitacion->tipo_habitacion_id == 3){


                array_push($habitacion_triple, $habitacion);

            }elseif ($habitacion->tipo_habitacion_id == 4) {

                array_push($habitacion_cuadruple, $habitacion);
                
            }elseif ($habitacion->tipo_habitacion_id == 5) {

                array_push($habitacion_quintuple, $habitacion);

            }elseif ($habitacion->tipo_habitacion_id == 6) {

                array_push($habitacion_matrimonial, $habitacion);

            }elseif ($habitacion->tipo_habitacion_id == 7) {

                array_push($habitacion_suite, $habitacion);

            }elseif ($habitacion->tipo_habitacion_id == 8) {

                array_push($habitacion_presidencial, $habitacion);
            }

        }

    $habitaciones_tipo = array(
            'tipos'           => [
        ['id' => 1, 'nombre' => 'individual',   'habitaciones' => $habitacion_individual    ],
        ['id' => 2, 'nombre' => 'doble',        'habitaciones' => $habitacion_doble         ],
        ['id' => 3, 'nombre' => 'triple',       'habitaciones' => $habitacion_triple        ],
        ['id' => 4, 'nombre' => 'cuadruple',    'habitaciones' => $habitacion_cuadruple     ],
        ['id' => 5, 'nombre' => 'quintuple',    'habitaciones' => $habitacion_quintuple     ],
        ['id' => 6, 'nombre' => 'matrimonial',  'habitaciones' => $habitacion_matrimonial   ],
        ['id' => 7, 'nombre' => 'suite',        'habitaciones' => $habitacion_suite         ],
        ['id' => 8, 'nombre' => 'presidencial', 'habitaciones' => $habitacion_presidencial  ],


    ],

    );


    return $habitaciones_tipo;


    }*/



    public function index(Request $request){

    	  if($request->has('propiedad_id')){
            $habitaciones = Habitacion::where('propiedad_id', $request->input('propiedad_id'))->with('estado')->with('tipoHabitacion')->with('precios.TipoMoneda')->with('equipamiento')->get();
            return $habitaciones;

        }
        
    }



	public function store(Request $request){

			$rules = array(

			'nombre' 		       => 'required',
            'disponibilidad_base'  => 'required|numeric',
			'piso'			       => 'required|numeric',
			'propiedad_id'         => 'required|numeric',
            'tipo_habitacion_id'   => 'required|numeric',
			'bano'      	       => 'required',
            'tv'        	       => 'required',
            'wifi'      	       => 'required',
            'frigobar'  	       => 'required',

			
		);

			$validator = Validator::make($request->all(), $rules);


 	     if ($validator->fails()) {

            $data = [

                'errors' => true,
                'msg' => $validator->messages(),

            ];

            return Response::json($data, 400);

        } else {


            $propiedad_id = $request->get('propiedad_id');
            $propiedad = Propiedad::where('id', $propiedad_id)->first();
            $cantidad_habitaciones = $propiedad->numero_habitaciones;

            $habitaciones_ingresadas = $propiedad->habitaciones->count();

            if($cantidad_habitaciones > $habitaciones_ingresadas){

            $habitacion                           = new Habitacion();
            $habitacion->nombre          	      = $request->get('nombre');
            $habitacion->disponibilidad_base      = $request->get('disponibilidad_base');
          	$habitacion->piso                     = $request->get('piso');
          	$habitacion->propiedad_id             = $request->get('propiedad_id');
            $habitacion->tipo_habitacion_id       = $request->get('tipo_habitacion_id');
            $habitacion->save();

            $equipamiento                    	  = new Equipamiento();
			$equipamiento ->bano              	  = $request->get('bano');
            $equipamiento ->tv                    = $request->get('tv');
            $equipamiento ->wifi                  = $request->get('wifi');
            $equipamiento ->frigobar              = $request->get('frigobar');
			$equipamiento ->habitacion_id		  = $habitacion->id; 
 			$equipamiento->save();

            $hab = Habitacion::where('id', $habitacion->id)->first();

            foreach ($request->get('precios') as $precio) {
            
                $precio_habitacion = $precio['precio_habitacion'];
                $tipo_moneda_id = $precio['tipo_moneda_id'];

                $precio                           = new Precio();
                $precio->precio_habitacion        = $precio_habitacion;
                $precio->tipo_moneda_id           = $tipo_moneda_id;
                $precio->habitacion_id            = $hab->id;
                $precio->save();
               
                
            }


            if(count($habitacion->precios) == count($propiedad->tipoMonedas)){

                $habitacion->update(array('estado_habitacion_id' => 1 ));

            }else{

                $habitacion->update(array('estado_habitacion_id' => 2 ));


            }
            


			     $data = [
                'errors' => false,
                'msg' => 'Habitacion creado satisfactoriamente',

            	];

			return Response::json($data, 201);

        }else{

                $data = [
                'errors' => true,
                'msg' => 'Habitaciones ya creadas',

                ];

            return Response::json($data, 400);



        }

        }



	}  




        public function update(Request $request, $id){


        $rules = array(

            'nombre'                => '',
            'precio_base'           => 'numeric',
            'precios'               => 'array',
            'disponibilidad_base'   => 'numeric',
            'piso'                  => 'numeric',
            'tipo_habitacion_id'    => 'numeric',
            'bano'                  => '',
            'tv'                    => '',
            'wifi'                  => '',
            'frigobar'              => '',
            
        );

    $validator = Validator::make($request->all(), $rules);


         if ($validator->fails()) {

            $data = [

                'errors' => true,
                'msg' => $validator->messages(),

            ];

            return Response::json($data, 400);

        } else {
            
            $propiedad = Propiedad::whereHas('habitaciones', function($query) use($id){

                    $query->where('id', $id);

                })->first();

            $habitacion = Habitacion::findOrFail($id);
            $habitacion->update($request->all());
            $habitacion->touch();
            


            $equipamiento = Equipamiento::findOrFail($id);

            $equipamiento->update($request->all());
            $equipamiento->touch();



            foreach ($request->get('precios') as $precio) {
                
                $id = $precio['id'];
                $precio_habitacion = $precio['precio_habitacion'];
                $tipo_moneda       = $precio['tipo_moneda_id'];

                $precio = Precio::where('id', $id)->first();
                $precio->update(array('precio_habitacion' => $precio_habitacion , 'tipo_moneda_id' => $tipo_moneda));

            }


            if(count($habitacion->precios) == count($propiedad->tipoMonedas)){

                $habitacion->update(array('estado_habitacion_id' => 1 ));

            }else{

                $habitacion->update(array('estado_habitacion_id' => 2 ));


            }






            $data = [

                'errors' => false,
                'msg' => 'Habitacion actualizada satisfactoriamente',

            ];

            return Response::json($data, 201);

        }

    }


         public function crearPrecio(Request $request){

                $habitacion_id = $request->input('habitacion_id');

                $propiedad = Propiedad::whereHas('habitaciones', function($query) use($habitacion_id){

                    $query->where('id', $habitacion_id);

                })->first();

                $habitacion = Habitacion::where('id', $habitacion_id)->first();

                $tipo_moneda_id =  $request->input('tipo_moneda_id');

                $precio_habitacion = $request->input('precio_habitacion');

                $precio                           = new Precio();
                $precio->precio_habitacion        = $precio_habitacion;
                $precio->tipo_moneda_id           = $tipo_moneda_id;
                $precio->habitacion_id            = $habitacion_id;
                $precio->save();

                if(count($habitacion->precios) == count($propiedad->tipoMonedas)){
                $habitacion->update(array('estado_habitacion_id' => 1 ));

                }else{

                $habitacion->update(array('estado_habitacion_id' => 2 ));

                }


                $data = [
                'errors' => false,
                'msg' => 'Precio creado satisfactoriamente',

                ];

            return Response::json($data, 201);





         }

         public function copiaPrecios(){



          $habitaciones = Habitacion::all();

          foreach ($habitaciones as $habitacion) {
              
             $precio_base = $habitacion->precio_base;
             $habitacion_id = $habitacion->id;

             $precio                           = new Precio();
             $precio->precio_habitacion        = $precio_base;
             $precio->tipo_moneda_id           = 1;
             $precio->habitacion_id            = $habitacion_id;
             $precio->save();



          }

             $data = [
                'errors' => false,
                'msg' => 'Precios creados satisfactoriamente',

                ];

            return Response::json($data, 201);




         }




        public function destroy($id)
        {



         $habitaciones = Habitacion::where('id', $id)->whereHas('calendarios', function($query){
           $query->where('reservas', 1);})->get();

         if(count($habitaciones) == 0 ){

        $habitacion = Habitacion::findOrFail($id);
        $habitacion->delete();


        $data = [

            'errors' => false,
            'msg'    => 'Habitacion eliminada satisfactoriamente',

        ];

        return Response::json($data, 202);

         }elseif (count($habitaciones) == 1) {


        $data = [

            'errors' => true,
            'msg'    => 'Metodo fallido',

        ];

        return Response::json($data, 401);


            
         }

        }


        public function getTipoHabitacion(){


         $tipoHabitacion = TipoHabitacion::all();
            return $tipoHabitacion;


        }

        public function getTipoMoneda(){

        $tipoMoneda = TipoMoneda::all();

        return $tipoMoneda;



        }




}




