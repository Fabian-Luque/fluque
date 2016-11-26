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

	Route::post('disponibilidad', 'HabitacionController@Disponibilidad');

	Route::post('reserva/habitacion', 'ReservaController@reserva');

	Route::get('reserva/propiedad', 'ReservaController@getReservas');

	Route::resource('user', 'UserController', ['except' => ['create', 'edit','store']]);
	Route::resource('propiedad', 'PropiedadController', ['except' => ['create', 'edit', 'store']]);
	Route::resource('habitacion', 'HabitacionController', ['except' => ['create', 'edit']]);
	Route::resource('servicio', 'ServicioController', ['except' => ['create', 'edit']]);
	Route::resource('reserva', 'ReservaController', ['except' => ['create', 'edit']]);
	Route::resource('cliente', 'ClienteController', ['except' => ['create', 'edit']]);
	Route::resource('tipo-habitacion', 'TipoHabitacionController', ['except' => ['create', 'edit']]);
	Route::resource('tipo-propiedad', 'TipoPropiedadController', ['except' => ['create', 'edit']]);




});




/*Route::group(['middleware' => 'auth'], function(){

Route::get('/', function () {
    return view('welcome');
  });


});*/