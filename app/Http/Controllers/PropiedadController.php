<?php

namespace App\Http\Controllers;

use App\ClasificacionMoneda;
use App\Http\Controllers\Controller;
use App\Precio;
use App\PrecioServicio;
use App\Propiedad;
use App\PropiedadMoneda;
use App\Servicio;
use App\TipoHabitacion;
use App\TipoPropiedad;
use Illuminate\Http\Request;
use Response;
use Validator;

class PropiedadController extends Controller
{

    public function ingresoServicio(Request $request)
    {

        if ($request->has('venta_servicio') && $request->has('propiedad_id') && $request->has('metodo_pago_id')) {

            $propiedad           = Propiedad::where('id', $request->input('propiedad_id'))->first();
            $metodo_pago_id      = $request->input('metodo_pago_id');
            $numero_operacion    = $request->input('numero_operacion');
            $tipo_comprobante_id = $request->input('tipo_comprobante_id');
            $numero_cheque       = $request->input('numero_cheque');

            if (!is_null($propiedad)) {

                $servicios = $request->input('venta_servicio');

                foreach ($servicios as $servicio) {

                    $servicio_id  = $servicio['servicio_id'];
                    $cantidad     = $servicio['cantidad'];
                    $precio_total = $servicio['precio_total'];

                    $serv                = Servicio::where('id', $servicio_id)->where('propiedad_id', $request->input('propiedad_id'))->first();
                    $cantidad_disponible = $serv->cantidad_disponible;

                    if (!is_null($serv)) {

                        if ($serv->categoria_id == 2) {

                            if ($cantidad >= 1) {

                                if ($serv->cantidad_disponible > 0) {

                                    if ($cantidad <= $serv->cantidad_disponible) {

                                        $servicio_id     = $serv->id;
                                        $servicio_nombre = $serv->nombre;

                                        $cantidad_disponible = $cantidad_disponible - $cantidad;

                                        $serv->update(array('cantidad_disponible' => $cantidad_disponible));

                                        $propiedad->vendeServicios()->attach($servicio_id, ['metodo_pago_id' => $metodo_pago_id, 'cantidad' => $cantidad, 'precio_total' => $precio_total, 'numero_operacion' => $numero_operacion, 'tipo_comprobante_id' => $tipo_comprobante_id, 'numero_cheque' => $numero_cheque]);

                                    } else {

                                        $data = array(

                                            'msj'    => " La cantidad ingresada es mayor al stock del producto",
                                            'errors' => true,

                                        );

                                        return Response::json($data, 400);

                                    }

                                } else {

                                    $data = array(

                                        'msj'    => " El servicio no tiene stock",
                                        'errors' => true,

                                    );

                                    return Response::json($data, 400);

                                }

                            } else {

                                $data = array(

                                    'msj'    => " La cantidad ingresada no corresponde",
                                    'errors' => true,

                                );

                                return Response::json($data, 400);

                            }

                        } elseif ($serv->categoria_id == 1) {

                            $propiedad->vendeServicios()->attach($servicio_id, ['metodo_pago_id' => $metodo_pago_id, 'cantidad' => $cantidad, 'precio_total' => $precio_total, 'numero_operacion' => $numero_operacion, 'tipo_comprobante_id' => $tipo_comprobante_id, 'numero_cheque' => $numero_cheque]);

                        }

                    } else {

                        $retorno = array(

                            'msj'    => "El servicio no pertenece a la propiedad",
                            'errors' => true,
                        );

                        return Response::json($retorno, 400);

                    }

                }

                $retorno = array(

                    'msj'   => "Servicios ingresados correctamente",
                    'erros' => false,
                );

                return Response::json($retorno, 201);

            } else {

                $data = array(

                    'msj'    => "Propiedad no encontrada",
                    'errors' => true,

                );

                return Response::json($data, 404);

            }

        } else {

            $retorno = array(

                'msj'    => "La solicitud esta incompleta",
                'errors' => true,
            );

            return Response::json($retorno, 400);

        }

    }

    public function index(Request $request)
    {

        if ($request->has('id')) {

            $propiedad = Propiedad::where('id', $request->input('id'))->with('tipoPropiedad')->with('tipoMonedas.clasificacionMonedas')->get();
            return $propiedad;

        }

    }

    public function show($id)
    {

        try {

            $propiedad = Propiedad::where('id', $id)->get();

            $tipos = TipoHabitacion::whereHas('habitaciones', function ($query) use ($id) {

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

    public function update(Request $request, $id)
    {

        $rules = array(

            'nombre'              => '',
            'tipo_propiedad_id'   => 'numeric',
            'numero_habitaciones' => 'numeric',
            'pais'                => '',
            'ciudad'              => '',
            'region'              => '',
            'direccion'           => '',
            'telefono'            => '',
            'email'               => '',
            'nombre_responsable'  => '',
            'descripcion'         => '',
            'iva'                 => 'numeric',
            'porcentaje_deposito' => 'numeric',

        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            $data = [

                'errors' => true,
                'msg'    => $validator->messages(),

            ];

            return Response::json($data, 400);

        } else {

            $propiedad = Propiedad::findOrFail($id);
            $propiedad->update($request->all());
            $propiedad->touch();

            $data = [

                'errors' => false,
                'msg'    => 'Propiedad actualizada satisfactoriamente',

            ];

            return Response::json($data, 201);

        }

    }

    public function ingresoMonedas(Request $request)
    {

        if ($request->has('propiedad_id') && $request->has('monedas')) {

            $propiedad             = Propiedad::where('id', $request->input('propiedad_id'))->with('habitaciones')->with('servicios')->first();
            $cantidad_habitaciones = count($propiedad->habitaciones);
            $cantidad_servicios    = count($propiedad->servicios);

            if (!is_null($propiedad)) {

                $monedas = $request->input('monedas');

                foreach ($monedas as $moneda) {

                    $clasificacion_moneda = $moneda['clasificacion_moneda_id'];
                    $tipo_moneda          = $moneda['tipo_moneda_id'];

                    $propiedad->clasificacionMonedas()->attach($clasificacion_moneda, ['tipo_moneda_id' => $tipo_moneda]);

                    if ($cantidad_habitaciones > 0) {

                        foreach ($propiedad->habitaciones as $habitacion) {

                            $habitacion_id = $habitacion->id;

                            $precio                    = new Precio();
                            $precio->precio_habitacion = null;
                            $precio->tipo_moneda_id    = $tipo_moneda;
                            $precio->habitacion_id     = $habitacion_id;
                            $precio->save();

                        }

                    }

                    if ($cantidad_servicios > 0) {

                        foreach ($propiedad->servicios as $servicio) {

                            $servicio_id = $servicio->id;

                            $precio                  = new PrecioServicio();
                            $precio->precio_servicio = null;
                            $precio->tipo_moneda_id  = $tipo_moneda;
                            $precio->servicio_id     = $servicio_id;
                            $precio->save();

                        }

                    }

                }

                $retorno = array(

                    'msj'   => "Moneda ingresada correctamente",
                    'erros' => false,
                );

                return Response::json($retorno, 201);

            } else {

                $retorno = array(

                    'msj'    => "Propiedad no encontrada",
                    'errors' => true,

                );

                return Response::json($retorno, 404);

            }

        } else {

            $retorno = array(

                'msj'    => "La solicitud esta incompleta",
                'errors' => true,
            );

            return Response::json($retorno, 400);

        }

    }

    public function eliminarMoneda(Request $request)
    {

        if ($request->has('id')) {

            $id = $request->input('id');

            $moneda         = PropiedadMoneda::where('id', $id)->first();
            $tipo_moneda_id = $moneda->tipo_moneda_id;
            $propiedad_id   = $moneda->propiedad_id;

            $precios_habitacion = Precio::where('tipo_moneda_id', $tipo_moneda_id)->whereHas('habitacion', function ($query) use ($propiedad_id) {

                $query->where('propiedad_id', $propiedad_id);

            })->get();

            $precios_servicio = PrecioServicio::where('tipo_moneda_id', $tipo_moneda_id)->whereHas('servicio', function ($query) use ($propiedad_id) {

                $query->where('propiedad_id', $propiedad_id);

            })->get();

            foreach ($precios_habitacion as $precio) {

                $precio->delete();
            }

            foreach ($precios_servicio as $precio) {

                $precio->delete();
            }

            $moneda->delete();

            $retorno = [

                'errors' => false,
                'msj'    => 'Moneda eliminada satisfactoriamente',

            ];

            return Response::json($retorno, 202);

        } else {

            $retorno = array(

                'msj'    => "La solicitud esta incompleta",
                'errors' => true,
            );

            return Response::json($retorno, 400);

        }

    }

    public function editarMoneda(Request $request, $id)
    {

        $rules = array(

            'clasificacion_moneda_id' => 'numeric',
            'tipo_moneda_id'          => 'numeric',

        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            $data = [

                'errors' => true,
                'msg'    => $validator->messages(),

            ];

            return Response::json($data, 400);

        } else {

            $moneda      = PropiedadMoneda::findOrFail($id);
            $tipo_moneda = $moneda->tipo_moneda_id;
            $moneda->update($request->all());
            $moneda->touch();

            $propiedad_id   = $moneda->propiedad_id;
            $tipo_moneda_id = $request->input('tipo_moneda_id');

            $precios_habitacion = Precio::where('tipo_moneda_id', $tipo_moneda)->whereHas('habitacion', function ($query) use ($propiedad_id) {

                $query->where('propiedad_id', $propiedad_id);

            })->get();

            $precios_servicio = PrecioServicio::where('tipo_moneda_id', $tipo_moneda)->whereHas('servicio', function ($query) use ($propiedad_id) {

                $query->where('propiedad_id', $propiedad_id);

            })->get();

            foreach ($precios_habitacion as $precio) {

                $precio->update(array('precio_habitacion' => null, 'tipo_moneda_id' => $tipo_moneda_id));
            }

            foreach ($precios_servicio as $servicio) {

                $servicio->update(array('precio_servicio' => null, 'tipo_moneda_id' => $tipo_moneda_id));
            }

            $data = [

                'errors' => false,
                'msg'    => 'Moneda actualizada satisfactoriamente',

            ];

            return Response::json($data, 201);

        }

    }

    public function getTipoPropiedad()
    {

        $TipoPropiedad = TipoPropiedad::all();
        return $TipoPropiedad;
    }

    public function getClasificacionMoneda()
    {

        $clasificacion = ClasificacionMoneda::all();
        return $clasificacion;

    }

}
