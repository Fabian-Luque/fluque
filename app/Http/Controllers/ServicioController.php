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
    
	public function index(Request $request)
    {
    	if($request->has('propiedad_id')){
            $propiedad_id = $request->input('propiedad_id');
            $propiedad    = Propiedad::where('id', $propiedad_id)->first();
            if (is_null($propiedad)) {
                $retorno  = array(
                    'msj'    => "Propiedad no encontrada",
                    'errors' => true);
                return Response::json($retorno, 404);
            }
        } else {
            $retorno = array(
                'msj'    => "No se envia propiedad_id",
                'errors' => true);
            return Response::json($retorno, 400);
        }

        $servicios = Propiedad::where('id', $request->input('propiedad_id'))->with('servicios.precios.TipoMoneda')->with('servicios.estado')->with('servicios.categoria')->get();
        return $servicios;
    }



	public function store(Request $request)
    {
		$rules = array(
			'nombre' 		      => 'required',
            'precios'             => 'array',
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

            $propiedad_id                      = $request->get('propiedad_id');
            $propiedad                         = Propiedad::where('id', $propiedad_id)->first();
           
            $servicio                          = new Servicio();
            $servicio->nombre          	       = $request->get('nombre');
            $servicio->cantidad_disponible     = $request->get('cantidad_disponible');
           	$servicio->categoria_id            = $request->get('categoria_id');
          	$servicio->propiedad_id            = $request->get('propiedad_id');
            $servicio->save();

            foreach ($request->get('precios') as $precio) {
                $precio_servicio               = $precio['precio_servicio'];
                $tipo_moneda_id                = $precio['tipo_moneda_id'];

                $precio                        = new PrecioServicio();
                $precio->precio_servicio       = $precio_servicio;
                $precio->tipo_moneda_id        = $tipo_moneda_id;
                $precio->servicio_id           = $servicio->id;
                $precio->save();
            }

            if(count($servicio->precios) == count($propiedad->tipoMonedas)){
                $servicio->update(array('estado_servicio_id' => 1 ));
            }else{
                $servicio->update(array('estado_servicio_id' => 2 ));
            }
            
			$data = [
                'errors' => false,
                'msg' => 'Servicio creado satisfactoriamente',
            ];
			return Response::json($data, 201);

        }
	}  


	public function update(Request $request, $id)
    {
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

            $propiedad = Propiedad::whereHas('servicios', function($query) use($id){
                $query->where('id', $id);
            })->first();

            $servicio = Servicio::findOrFail($id);
            $servicio->update($request->all());
            $servicio->touch();

            foreach ($request->get('precios') as $precio) {
                
                $id              = $precio['id'];
                $precio_servicio = $precio['precio_servicio'];
                $tipo_moneda     = $precio['tipo_moneda_id'];

                $precio          = PrecioServicio::where('id', $id)->first();
                $precio->update(array('precio_servicio' => $precio_servicio, 'tipo_moneda_id' => $tipo_moneda));

            }

            if(count($servicio->precios) == count($propiedad->tipoMonedas)){
                $servicio->update(array('estado_servicio_id' => 1 ));
            }else{
                $servicio->update(array('estado_servicio_id' => 2 ));
            }

            $data = [
                'errors' => false,
                'msg' => 'Servicio actualizado satisfactoriamente',
            ];
            return Response::json($data, 201);

        }
	}  


    public function crearPrecio(Request $request)
    {

        $servicio_id  =  $request->input('servicio_id');

        $propiedad = Propiedad::whereHas('servicios', function($query) use($servicio_id){

            $query->where('id', $servicio_id);

        })->first();

        $servicio = Servicio::where('id', $servicio_id)->first();

        $tipo_moneda_id =  $request->input('tipo_moneda_id');

        $precio_servicio = $request->input('precio_servicio');

        $precio                           = new PrecioServicio();
        $precio->precio_servicio          = $precio_servicio;
        $precio->tipo_moneda_id           = $tipo_moneda_id;
        $precio->servicio_id              = $servicio_id;
        $precio->save();

        if(count($servicio->precios) == count($propiedad->tipoMonedas)){

        $servicio->update(array('estado_servicio_id' => 1 ));

        }else{

        $servicio->update(array('estado_servicio_id' => 2 ));
        }

        $data = [
        'errors' => false,
        'msg' => 'Precio creado satisfactoriamente',

        ];

        return Response::json($data, 201);

    }



	public function destroy($id)
    {
        $servicio = Servicio::findOrFail($id);
        $servicio->delete();

        $data = [
            'errors' => false,
            'msg'    => 'Servicio eliminado satisfactoriamente',
        ];
        return Response::json($data, 202);

    }


    public function getCategoria()
    {
        $categorias = Categoria::all();
        return $categorias;
    }


}
