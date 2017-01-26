<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use App\User;
use App\Propiedad;
use App\Habitacion;
use Excel;
use DB;
use \Carbon\Carbon;

class ExcelController extends Controller
{



	public function importUsuarios(){

		if(Input::hasFile('file')){
			$path = Input::file('file')->getRealPath();

			$data = Excel::load($path, function($reader) {
			})->get();

			$users = [];
			if(!empty($data)){

				foreach ($data as $usuarios) {
					foreach ($usuarios as $usuario) {
						if($usuario->name){
						array_push($users, $usuario);

						}
					}

					
				}

			foreach ($users as $key => $value) {
						$usuario=new User;
						$usuario->name= $value->name;
						$usuario->email= $value->email;
						$usuario->password= $value->password;
						$usuario->phone= $value->phone;
						$usuario->remember_token= null;
						$usuario->created_at= $value->created_at;
						$usuario->updated_at= $value->updated_at;
						$usuario->deleted_at= null;
						$usuario->save();


				}

			}
		}
		return "excel importado";
	}


	public function importPropiedades(){


		if(Input::hasFile('file')){
			$path = Input::file('file')->getRealPath();

			$data = Excel::load($path, function($reader) {
			})->get();

			$prop = [];
			if(!empty($data)){

				foreach ($data as $propiedades) {
					foreach ($propiedades as $propiedad) {
						if($propiedad->nombre){
						array_push($prop, $propiedad);

						}
					}

					
				}


			foreach ($prop as $key => $value) {
						$Propiedad=new Propiedad;
						$Propiedad->nombre= $value->nombre;
						$Propiedad->numero_habitaciones= $value->numero_habitaciones;
						$Propiedad->pais= $value->pais;
						$Propiedad->ciudad= $value->ciudad;
						$Propiedad->region= $value->region;
						$Propiedad->direccion= $value->direccion;
						$Propiedad->telefono= $value->telefono;
						$Propiedad->email= $value->email;
						$Propiedad->nombre_responsable= $value->nombre_responsable;
						$Propiedad->descripcion= $value->descripcion;
						$Propiedad->iva= $value->iva;
						$Propiedad->porcentaje_deposito= $value->porcentaje_deposito;
						$Propiedad->user_id= $value->user_id;
						$Propiedad->tipo_propiedad_id= $value->tipo_propiedad_id;
						$Propiedad->created_at= $value->created_at;
						$Propiedad->updated_at= $value->updated_at;
						$Propiedad->deleted_at= $value->updated_at;
						$Propiedad->save();


				}

			}


			
		}

		return "excel importado";



	}

		public function importHabitaciones(){


		if(Input::hasFile('file')){
			$path = Input::file('file')->getRealPath();

			$data = Excel::load($path, function($reader) {
			})->get();

			$hab = [];
			if(!empty($data)){

				foreach ($data as $habitaciones) {
					foreach ($habitaciones as $habitacion) {
						if($habitacion->nombre){
						array_push($hab, $habitacion);

						}
					}

					
				}



			foreach ($hab as $key => $value) {
						$habitacion=new Habitacion;
						$habitacion->nombre= $value->nombre;
						$habitacion->precio_base= $value->precio_base;
						$habitacion->disponibilidad_base= $value->disponibilidad_base;
						$habitacion->piso= $value->piso;
						$habitacion->propiedad_id= $value->propiedad_id;
						$habitacion->tipo_habitacion_id= $value->tipo_habitacion_id;
						$habitacion->created_at= $value->created_at;
						$habitacion->updated_at= $value->updated_at;
						$habitacion->deleted_at= $value->updated_at;
						$habitacion->save();


				}

			}


			
		}

		return "excel importado";



	}




}
