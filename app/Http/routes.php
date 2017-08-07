<?php

use Illuminate\Http\Response as HttpResponse;

// rutas dashboard
Route::group(['prefix' => 'dash', 'middleware' => ['auth']], 
	function() {
    	Route::get(
			'adminhome', 
			function() {
    			return View::make('administrador.home_admin');
			}
		);

		Route::get(
			'adminprop', 
			function() {
    			return View::make('administrador.prop');
			}
		);

		Route::get(
			'adminreguser', 
			function() {
    			return View::make('administrador.reguser');
			}
		);
		Route::get('adminreguser', 'DashControllers\UserDashController@getViewTipoPropiedad');

		Route::get('adminreguser', 'DashControllers\UserDashController@getViewPropiedad');

		






		Route::get(
			'adminuser', 
			function() {
    			return View::make('administrador.user');
			}
		);

		Route::get(
			'edituser', 
			function() {
    			return View::make('administrador.editmodal');
			}
		);
		


		Route::get('adminuser', 'DashControllers\UserDashController@ReadUser');
				
		Route::post('crear/user', 'DashControllers\UserDashController@CreateUser')->name('crear.user');



		Route::post('obtener/user', 'DashControllers\UserDashController@ReadUser');
		Route::post('actualizar/user', 'DashControllers\UserDashController@UpdateUser');
		Route::post('eliminar/user', 'DashControllers\UserDashController@DeleteUser');
	}
);
////


Route::group(['as' => 'api.jarvis.'], function(){

  Route::post('registro', 'UserController@store');
  Route::post('/signin', 'ApiAuthController@signin');

	Route::group(['middleware' => ['jwt.auth']], function () {

	Route::post('reserva/habitacion', 'ReservaController@reserva');
	Route::get('reserva/propiedad', 'ReservaController@getReservas');
	Route::get('tipo-fuente', 'ReservaController@getTipoFuente');
	Route::get('tipo-comprobante', 'ReservaController@getTipoComprobante');
	Route::get('metodo-pago', 'ReservaController@getMetodoPago');
	Route::get('estado-reserva', 'ReservaController@getEstadoReserva');
	Route::get('tipo-habitacion', 'HabitacionController@getTipoHabitacion');
	Route::get('categorias', 'ServicioController@getCategoria');
	Route::get('tipo-propiedad', 'PropiedadController@getTipoPropiedad');
	Route::get('tipo-cliente', 'ClienteController@getTipoCliente');
	Route::post('ingreso/huesped', 'HuespedController@ingresoHuesped');
	Route::get('huesped/reserva', 'HuespedController@getHuespedes');
	Route::post('ingreso/consumo', 'HuespedController@ingresoConsumo');
	Route::post('calificacion', 'ClienteController@calificacion');
	Route::post('pago', 'ReservaController@pagoReserva');
	Route::post('panel', 'ReservaController@panel');
	Route::get('calendario', 'ReservaController@calendario');
	Route::post('usuarios/excel','ExcelController@importUsuarios');
	Route::post('propiedades/excel','ExcelController@importPropiedades');
	Route::post('habitaciones/excel','ExcelController@importHabitaciones');
	Route::post('servicios/excel','ExcelController@importServicios');
	Route::delete('consumo/{id}', 'HuespedController@eliminarConsumo');
	Route::post('pdf/estado/cuenta', 'PDFController@estadoCuenta');
	Route::post('pdf/reporte', 'PDFController@reporte');
	Route::post('pdf/huesped', 'PDFController@huesped');
	Route::post('pdf/checkin', 'PDFController@checkin');
	Route::post('pdf/pagos', 'PDFController@pagos');
	Route::post('ingreso/servicio', 'PropiedadController@ingresoServicio');
	Route::post('ingreso/servicio/cliente', 'ClienteController@ingresoServicio');
	Route::get('cliente/empresa', 'ClienteController@getClientes');
	Route::post('pago/consumo', 'ReservaController@pagoConsumo');
	Route::get('cliente/email', 'ClienteController@getCliente');
	Route::get('buscar/email', 'ClienteController@buscarEmail');
	Route::get('buscar/rut', 'ClienteController@buscarRut');
	Route::get('disponibilidad', 'HabitacionController@disponibilidad');
	Route::get('editar/reserva', 'ReservaController@editarReserva');
	Route::get('tipo-moneda', 'HabitacionController@getTipoMoneda');
	Route::post('crear/precio/habitacion', 'HabitacionController@crearPrecio');
	Route::post('crear/precio/servicio', 'ServicioController@crearPrecio');
	Route::post('cambiar/habitacion', 'ReservaController@cambiarHabitacion');
	Route::get('clasificacion/moneda', 'PropiedadController@getClasificacionMoneda');
	Route::post('ingreso/moneda/propiedad', 'PropiedadController@ingresoMonedas');
	Route::post('eliminar/moneda/propiedad', 'PropiedadController@eliminarMoneda');
	Route::put('editar/moneda/{id}', 'PropiedadController@editarMoneda');
	Route::get('reporte', 'PropiedadController@reportesDiario');
	Route::post('crear/pais', 'PropiedadController@crearPais');
	Route::get('paises', 'PropiedadController@getPaises');
	Route::get('regiones', 'PropiedadController@getRegiones');
	Route::post('calendario/temporada', 'TemporadaController@calendario');
	Route::post('precio/temporada', 'HabitacionController@temporada');
	Route::get('precio/habitacion', 'HabitacionController@precioHabitacion');
	Route::get('periodo/calendario', 'TemporadaController@getCalendario');
	Route::post('eliminar/calendario', 'TemporadaController@eliminarCalendario');
	Route::get('temporada/precios', 'TemporadaController@getPreciosTemporadas');
	Route::post('editar/temporadas', 'TemporadaController@editarTemporadas');
	Route::get('reportes', 'PropiedadController@reportes');
	Route::get('reportes/pago', 'PropiedadController@pagos');
	Route::post('crear/zona/horaria', 'PropiedadController@crearZona');
	Route::get('zonas/horarias', 'PropiedadController@getZonasHorarias');
	Route::put('pago/{id}', 'ReservaController@editarPago');
	Route::delete('pago/{id}', 'ReservaController@eliminarPago');
	Route::post('reserva/busqueda', 'ReservaController@filtroReservas');

	Route::resource('user', 'UserController', ['except' => ['create', 'edit','store']]);
	Route::resource('propiedad', 'PropiedadController', ['except' => ['create', 'edit', 'store']]);
	Route::resource('habitacion', 'HabitacionController', ['except' => ['create', 'edit']]);
	Route::resource('servicio', 'ServicioController', ['except' => ['create', 'edit']]);
	Route::resource('reserva', 'ReservaController', ['except' => ['create', 'edit']]);
	Route::resource('cliente', 'ClienteController', ['except' => ['create', 'edit']]);
	Route::resource('huesped', 'HuespedController', ['except' => ['create', 'edit']]);
	Route::resource('temporada', 'TemporadaController', ['except' => ['create', 'edit']]);
	Route::resource('tipo/habitacion', 'TipoHabitacionController', ['except' => ['create', 'edit']]);

});



});




Route::auth();




Route::get('/', function () {
    return view('welcome');
  });

/*Route::get('users', 'UserController@index');*/


/*
Route::group(['middleware' => 'cors'], function(){

	Route::post('/auth_login', 'ApiAuthController@userAuth');

	Route::post('reserva/habitacion', 'ReservaController@reserva');

	Route::get('reserva/propiedad', 'ReservaController@getReservas');

	Route::get('tipo-fuente', 'ReservaController@getTipoFuente');

	Route::get('tipo-comprobante', 'ReservaController@getTipoComprobante');

	Route::get('metodo-pago', 'ReservaController@getMetodoPago');

	Route::get('estado-reserva', 'ReservaController@getEstadoReserva');

	Route::get('tipo-habitacion', 'HabitacionController@getTipoHabitacion');

	Route::get('categorias', 'ServicioController@getCategoria');

	Route::get('tipo-propiedad', 'PropiedadController@getTipoPropiedad');

	Route::get('tipo-cliente', 'ClienteController@getTipoCliente');

	Route::post('ingreso/huesped', 'HuespedController@ingresoHuesped');

	Route::get('huesped/reserva', 'HuespedController@getHuespedes');

	Route::post('ingreso/consumo', 'HuespedController@ingresoConsumo');

	Route::post('calificacion', 'ClienteController@calificacion');

	Route::post('pago', 'ReservaController@pagoReserva');

	Route::post('panel', 'ReservaController@panel');

	Route::get('calendario', 'ReservaController@calendario');

	Route::post('usuarios/excel','ExcelController@importUsuarios');

	Route::post('propiedades/excel','ExcelController@importPropiedades');

	Route::post('habitaciones/excel','ExcelController@importHabitaciones');

	Route::post('servicios/excel','ExcelController@importServicios');

	Route::delete('consumo/{id}', 'HuespedController@eliminarConsumo');
	
	Route::post('pdf/estado/cuenta', 'PDFController@estadoCuenta');

	Route::post('pdf/reporte', 'PDFController@reporte');

	Route::post('pdf/huesped', 'PDFController@huesped');

	Route::post('ingreso/servicio', 'PropiedadController@ingresoServicio');

	Route::post('ingreso/servicio/cliente', 'ClienteController@ingresoServicio');

	Route::get('cliente/empresa', 'ClienteController@getClientes');

	Route::post('pago/consumo', 'ReservaController@pagoConsumo');

	Route::get('cliente/email', 'ClienteController@getCliente');

	Route::get('buscar/email', 'ClienteController@buscarEmail');

	Route::get('buscar/rut', 'ClienteController@buscarRut');

	Route::get('disponibilidad', 'HabitacionController@disponibilidad');

	Route::get('editar/reserva', 'ReservaController@editarReserva');

	Route::get('tipo-moneda', 'HabitacionController@getTipoMoneda');

	Route::post('crear/precio/habitacion', 'HabitacionController@crearPrecio');

	Route::post('crear/precio/servicio', 'ServicioController@crearPrecio');

	Route::post('cambiar/habitacion', 'ReservaController@cambiarHabitacion');

	Route::get('clasificacion/moneda', 'PropiedadController@getClasificacionMoneda');

	Route::post('ingreso/moneda/propiedad', 'PropiedadController@ingresoMonedas');

	Route::post('eliminar/moneda/propiedad', 'PropiedadController@eliminarMoneda');

	Route::put('editar/moneda/{id}', 'PropiedadController@editarMoneda');

	Route::get('reporte', 'PropiedadController@reportesDiario');

	Route::post('crear/pais', 'PropiedadController@crearPais');

	Route::get('paises', 'PropiedadController@getPaises');

	Route::get('regiones', 'PropiedadController@getRegiones');

	Route::post('calendario/temporada', 'TemporadaController@calendario');

	Route::post('precio/temporada', 'HabitacionController@temporada');

	Route::get('precio/habitacion', 'HabitacionController@precioHabitacion');

	Route::get('periodo/calendario', 'TemporadaController@getCalendario');

	Route::post('eliminar/calendario', 'TemporadaController@eliminarCalendario');

	Route::get('temporada/precios', 'TemporadaController@getPreciosTemporadas');

	Route::post('editar/temporadas', 'TemporadaController@editarTemporadas');

	Route::get('reportes', 'PropiedadController@reportes');

	Route::post('crear/zona/horaria', 'PropiedadController@crearZona');

	Route::get('zonas/horarias', 'PropiedadController@getZonasHorarias');

	Route::put('pago/{id}', 'ReservaController@editarPago');

	Route::delete('pago/{id}', 'ReservaController@eliminarPago');


	Route::resource('user', 'UserController', ['except' => ['create', 'edit','store']]);
	Route::resource('propiedad', 'PropiedadController', ['except' => ['create', 'edit', 'store']]);
	Route::resource('habitacion', 'HabitacionController', ['except' => ['create', 'edit']]);
	Route::resource('servicio', 'ServicioController', ['except' => ['create', 'edit']]);
	Route::resource('reserva', 'ReservaController', ['except' => ['create', 'edit']]);
	Route::resource('cliente', 'ClienteController', ['except' => ['create', 'edit']]);
	Route::resource('huesped', 'HuespedController', ['except' => ['create', 'edit']]);
	Route::resource('temporada', 'TemporadaController', ['except' => ['create', 'edit']]);
	Route::resource('tipo/habitacion', 'TipoHabitacionController', ['except' => ['create', 'edit']]);


});
*/


/*Route::group(['middleware' => 'auth'], function(){

Route::get('/', function () {
    return view('welcome');
  });


});*/
/* ================== Homepage + Admin Routes ================== */


