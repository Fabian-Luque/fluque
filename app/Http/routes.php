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

	Route::get('getHabitaciones/{id}', 'HabitacionController@getHabitaciones');


	Route::resource('user', 'UserController', ['except' => ['create', 'edit','store']]);
	Route::resource('propiedad', 'PropiedadController', ['except' => ['create', 'edit', 'store']]);
	Route::resource('habitacion', 'HabitacionController', ['except' => ['create', 'edit']]);
	/*Route::resource('equipamiento', 'equipamientoController', ['except' => ['create', 'edit']]);*/


});




/*Route::group(['middleware' => 'auth'], function(){

Route::get('/', function () {
    return view('welcome');
  });


});*/