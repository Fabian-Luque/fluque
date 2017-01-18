<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/



Route::auth();


/*Route::get('users', 'UserController@index');*/


Route::get('/', function () {
    return view('welcome');
  });

Route::group(['middleware' => 'cors'], function(){

	Route::post('registro', 'UserController@store');
	
	Route::post('/auth_login', 'ApiAuthController@userAuth');

	Route::post('reserva/habitacion', 'ReservaController@reserva');

	Route::get('reserva/propiedad', 'ReservaController@getReservas');

	Route::get('tipo-fuente', 'ReservaController@getTipoFuente');

	Route::get('metodo-pago', 'ReservaController@getMetodoPago');

	Route::get('estado-reserva', 'ReservaController@getEstadoReserva');

	Route::get('tipo-habitacion', 'HabitacionController@getTipoHabitacion');

	Route::get('tipo-propiedad', 'PropiedadController@getTipoPropiedad');

	Route::get('tipo-cliente', 'ClienteController@getTipoCliente');

	Route::post('ingreso/huesped', 'HuespedController@ingresoHuesped');

	Route::get('huesped/reserva', 'HuespedController@getHuespedes');

	Route::post('ingreso/consumo', 'HuespedController@ingresoConsumo');

	Route::post('calificacion', 'ClienteController@calificacion');

	Route::post('pago', 'ReservaController@pagoReserva');

	Route::post('panel', 'ReservaController@panel');

	Route::get('calendario', 'ReservaController@calendario');

	Route::delete('consumo/{id}', 'HuespedController@eliminarConsumo');

	Route::resource('user', 'UserController', ['except' => ['create', 'edit','store']]);
	Route::resource('propiedad', 'PropiedadController', ['except' => ['create', 'edit', 'store']]);
	Route::resource('habitacion', 'HabitacionController', ['except' => ['create', 'edit']]);
	Route::resource('servicio', 'ServicioController', ['except' => ['create', 'edit']]);
	Route::resource('reserva', 'ReservaController', ['except' => ['create', 'edit']]);
	Route::resource('cliente', 'ClienteController', ['except' => ['create', 'edit']]);
	Route::resource('huesped', 'HuespedController', ['except' => ['create', 'edit']]);



});


Route::get('pdf', function(){


	$pdf = PDF::loadView('vista');
	return $pdf->download('archivo.pdf');





});



/*Route::group(['middleware' => 'auth'], function(){

Route::get('/', function () {
    return view('welcome');
  });


});*/