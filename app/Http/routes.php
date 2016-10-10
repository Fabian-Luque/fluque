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


Route::get('users', 'UserController@index');

Route::post('registro', 'UserController@store');

Route::get('/', function () {
    return view('welcome');
  });

Route::group(['middleware' => 'cors'], function(){

	Route::post('/auth_login', 'ApiAuthcontroller@userAuth');


});




/*Route::group(['middleware' => 'auth'], function(){

Route::get('/', function () {
    return view('welcome');
  });







});*/