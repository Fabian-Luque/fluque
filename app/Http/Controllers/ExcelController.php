<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use App\User;
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




}
