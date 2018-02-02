<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Storage;
use Aws\S3\Exception\S3Exception;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Illuminate\Support\Facades\Validator;
use \Carbon\Carbon;
use Response;

class S3Controller extends Controller {
	public function UpdateNameDirectory(Request $request) { // amazon s3
		try {
			if ($request->has('nombre_prop') && $request->has('new_nombre')) {
				if ($this->SearchDirectory($request->nombre_prop)['existe'] == true) {
					$imgs_s3 = collect([]);
					try {
						$files = Storage::disk('s3')->allFiles(
							$request->nombre_prop
						);

						for ($i = 0; $i < count($files); $i++) { 
							$date = Carbon::createFromTimestamp(
								explode(
									"/", 
									explode(
										"_", 
										$files[$i]
									)[0]
								)[1]
							)->toDateTimeString();

							$imgs_s3->push([
								'data' => Storage::disk('s3')->get(
									$files[$i]
								),
								'name' => explode(
									"/", 
									explode(
										"_", 
										$files[$i]
									)[0]
								)[1].".jpg", 
								'created_at' => $date
							]);
						}

						Storage::disk('s3')->deleteDirectory(
							$request->nombre_prop
						);

						foreach ($imgs_s3 as $img) {
							Storage::disk('s3')->put(
								$request->new_nombre."/".$img['name'],//$imageName, 
								$img['data'], 
								'public'
							);
						}
						$retorno['error'] = false;
						$retorno['msj'] = "Nombre del directorio actualizado a: ".$request->new_nombre;
					} catch (\Exception $e) {
						$retorno['error'] = true;
						$retorno['msj'] = "Archivo no encontrado: ".$e->getMessage()." Linea: ".$e->getLine();
					}
				} else {
					$retorno['error'] = true;
					$retorno['msj'] = 'El directorio no existe en amazon';
				}
			} elseif ($request->has('nombre_prop')) {
				$files = Storage::disk('s3')->allFiles(
						$request->nombre_prop
					);
				$retorno['error'] = true;
					$retorno['msj'] = 'El directorio no existe en amazon';
			} else {
				$retorno['error'] = true;
				$retorno['msj'] = "Datos requeridos";
			}
		} catch (S3Exception $e) {
			$retorno['error'] = true;
			$retorno['msj'] = $e->getMessage();
		} 
		return Response::json($retorno);
	}

	public function UpdateImage(Request $request) {
		try {
			if ($request->has('nombre_prop') && $request->has('nombre') && $request->has('new_nombre')) {
				if ($this->SearchDirectory($request->nombre_prop)['existe'] == true) {
					try {
						$img = Storage::disk('s3')->put(
							$request->nombre_prop."/".$request->new_nombre,
							Storage::disk('s3')->get(
								$request->nombre_prop."/".$request->nombre
							),
							'public'
						);

						if ($img == true) {
							Storage::disk('s3')->delete(
								$request->nombre_prop."/".$request->nombre
							);

							$retorno['error'] = false;
							$retorno['msj'] = "Actualizacion exitosa";
						} else {
							$retorno['error'] = true;
							$retorno['msj'] = "Error al actualizar";
						}
					} catch (\Exception $e) {
						$retorno['error'] = true;
						$retorno['msj'] = "Archivo no encontrado: ".$e->getMessage();
					}
				} else {
					$retorno['error'] = true;
					$retorno['msj'] = 'El directorio no existe en amazon';
				}
			} else {
				$retorno['error'] = true;
				$retorno['msj'] = "Datos requeridos";
			}
		} catch (S3Exception $e) {
			$retorno['error'] = true;
			$retorno['msj'] = $e->getMessage();
		} 
		return Response::json($retorno);
	}

	public function DeleteImage(Request $request) {
		try {
			if ( $request->has('nombre') && $request->has('nombre_prop')) {
				$imageName = Storage::disk('s3')->delete(
					$request->nombre_prop."/".$request->nombre
				);

				$retorno['error'] = false;
				$retorno['msj'] = 'Delete exitoso';
				$retorno['img'] = 'https://s3-sa-east-1.amazonaws.com/gofeels-props-images/'.$request->nombre_prop."/".$request->nombre;
			} else {
				$retorno['error'] = true;
				$retorno['msj'] = "Datos requeridos";
			}
		} catch (S3Exception $e) {
			$retorno['error'] = true;
			$retorno['msj'] = $e->getMessage();
		} 
		return Response::json($retorno);
	}

	public function DeleteDirectory(Request $request) { // amazon s3
		try {
			if ( $request->has('directorio')) {
				if ($this->SearchDirectory($request->directorio)['existe'] == true) {
					$imageName = Storage::disk('s3')->deleteDirectory(
						$request->directorio
					);

					$retorno['error'] = false;
					$retorno['msj'] = 'Delete exitoso';
				} else {
					$retorno['error'] = true;
					$retorno['msj'] = 'El directorio no existe en amazon';
				}
			} else {
				$retorno['error'] = true;
				$retorno['msj'] = "Datos requeridos";
			}
		} catch (S3Exception $e) {
			$retorno['error'] = true;
			$retorno['msj'] = $e->getMessage();
		} 
		return Response::json($retorno);
	}
	
	public function UploadImage(Request $request) {
		$validator = Validator::make(
			$request->all(), 
			array(
				'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
				'nombre_prop' => 'required',
				'nombre' => 'required'
			)
		);
		if ($validator->fails()) {
			$retorno['errors'] = true;
			$retorno["msj"] = $validator->errors();
		} else {
			try {
				$imageName = time().'.'.$request->file->getClientOriginalExtension();
				$image = $request->file('file');
				$t = Storage::disk('s3')->put(
					$request->nombre_prop."/".$request->nombre,//$imageName, 
					file_get_contents($image), 
					'public'
				);
				$imageName = Storage::disk('s3')->url($imageName);

				$retorno['error'] = false;
				$retorno['msj'] = 'Upload exitoso';
				$retorno['img'] = 'https://s3-sa-east-1.amazonaws.com/gofeels-props-images/'.$request->nombre_prop."/".$request->nombre;
			} catch (S3Exception $e) {
				$retorno['error'] = true;
				$retorno['msj'] = $e->getMessage();
			} 
		}
		return Response::json($retorno);
	}

	public function GetAllImagesByDir(Request $request) {
		if ($request->has('nombre_prop') && $request->has('nombre')) {
			try {
				$imagenes = collect([]);

				$date = Carbon::createFromTimestamp(
					explode(
						"_", 
						$request->nombre
					)[0]
				)->toDateTimeString();
				
				$imagenes->push([
					'nombre' => $request->nombre, 
					'created_at' => $date
				]);

				$retorno['error'] = false;
				$retorno['msj'] = "Listado de imagenes";
				$retorno['url_base'] = "https://s3-sa-east-1.amazonaws.com/gofeels-props-images/";
				$retorno['dir'] = $request->nombre_prop."/";
				$retorno['lista'] = $imagenes;
			} catch (S3Exception $e) {
				$retorno['error'] = true;
				$retorno['msj'] = $e->getMessage();
			}
		} elseif ($request->has('nombre_prop')) {
			try {
				if ($this->SearchDirectory($request->nombre_prop)['existe'] == true) {
					$files = Storage::disk('s3')->allFiles(
						$request->nombre_prop
					);

					$imagenes = collect([]);

					for ($i = 0; $i < count($files); $i++) {
						$im = explode(
							"/", 
							$files[$i]
						); 
						$date = Carbon::createFromTimestamp(
							explode(
								"_", 
								$im[count($im) - 1]
							)[0]
						)->toDateTimeString();
						
						$imagenes->push([
							'nombre' => $im[count($im) - 1], 
							'created_at' => $date
						]);
					}

					$imagenes->sortBy('created_at');
					//dd(Carbon::now('CLST')->toDateTimeString());
					//dd(Carbon::createFromTimestamp(1517499393)->toDateTimeString());
					$retorno['error'] = false;
					$retorno['msj'] = "Lista de imagenes";
					$retorno['url_base'] = "https://s3-sa-east-1.amazonaws.com/gofeels-props-images/";
					$retorno['dir'] = $request->nombre_prop."/";
					$retorno['img'] = $imagenes;
				} else {
					$retorno['error'] = true;
					$retorno['msj'] = 'El directorio no existe en amazon';
				}
			} catch (S3Exception $e) {
				$retorno['error'] = true;
				$retorno['msj'] = $e->getMessage();
			}  
		} else {
			$retorno['error'] = true;
			$retorno['msj'] = "Datos requeridos";
		}
		return Response::json($retorno);
	}

	public function GetImage(Request $request) {
		try {
			if ( $request->has('name') &&  $request->has('nombre_prop')) {
				$image = Storage::disk('s3')->get(
					$request->nombre_prop."/".$request->name
				);
				$response = new Response($image, 200, [
					'Content-Type' => $attachment->type,
				]);
				$retorno['error'] = true;
				$retorno['msj'] = $response;
			} else {
				$retorno['error'] = true;
				$retorno['msj'] = "Datos requeridos";
			}
		} catch (S3Exception $e) {
			$retorno['error'] = true;
			$retorno['msj'] = "Error: ".$e->getMessage();
		} catch (FileNotFoundException $e) {
			$retorno['error'] = true;
			$retorno['msj'] = "Error: ".$e->getMessage();
		}
		return Response::json($retorno);
	}
}
