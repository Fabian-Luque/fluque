<?php

use Illuminate\Http\Response as HttpResponse;

Route::auth();

Route::get(
	'/', 
	function () {
		return view('auth.login');
	}
);

Route::post('guardar/ubicacion/propiedad', 'GeoController@UbicacionCreate');
Route::post('googlemaps', 'GeoController@GoogleMaps');



Route::get('hotelescercanos', 'GeoController@Gmaps');
Route::get(
	'/gmap', 
	function () {
		return view('administrador.gmap');
	}
);

//////////////////////// rutas dash ////////////////////////////


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

		Route::get('adminprop', 'DashControllers\UserDashController@getViewPropiedad');

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

		Route::get(
			'buscauser', 
			function() {
    			return View::make('administrador.accionesC');
			}
		);
		
		Route::get('adminuser', 'DashControllers\UserDashController@ReadUser');
				
		Route::post('crear/user', 'DashControllers\UserDashController@CreateUser')->name('crear.user');

		Route::post('obtener/user', 'DashControllers\UserDashController@ReadUser');
		Route::post('actualizar/user', 'DashControllers\UserDashController@UpdateUser')->name('editar.user');
		Route::post('eliminar/user', 'DashControllers\UserDashController@DeleteUser');
	}
);
//////////////////////// rutas dash  ///////////////////////////////////////

Route::post('my', 'MyAllocatorController@ejm');



	Route::post('reset/password', 'ApiAuthController@ResetPassword')->name('cambiar.pass');

	Route::post('resetpass/email', 'CorreoController@sendmail')->name('reset.pass.sendmail');
	
	Route::get(
		'sendmailreset', 
		function() {
    		return View::make('administrador.sendmailresetpass');
		}
	);

	Route::get(
		'resetpass', 
		function() {
    		return View::make('administrador.resetpass');
		}
	);

	Route::get('reset/password/{token}', 'ApiAuthController@ResetPassword');

Route::group(['as' => 'api.jarvis.'], function() {

	Route::post('registro', 'UserController@store');
	Route::post('/signin', 'ApiAuthController@signin');


	Route::group(['middleware' => ['jwt.auth']], function () {
		Route::post('cambio/password', 'ApiAuthController@ResetPassUser');
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
		Route::post('pdf/estado/cuenta/resumen', 'PDFController@estadoCuentaResumen');
		Route::post('pdf/reporte', 'PDFController@reporte');
		Route::post('pdf/reporte/financiero', 'PDFController@reporteFinanciero');
		Route::post('pdf/entradas', 'PDFController@entradas');
		Route::post('pdf/salidas', 'PDFController@salidas');
		Route::post('pdf/huesped', 'PDFController@huesped');
		Route::post('pdf/checkin', 'PDFController@checkin');
		Route::post('pdf/pagos', 'PDFController@pagos');
		Route::post('pdf/reservas', 'PDFController@reservas');
		Route::post('pdf/comprobante/reserva', 'PDFController@comprobanteReserva');
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
		Route::get('tipo/cobros', 'PropiedadController@getTipoCobro');
		Route::post('editar/precios', 'TipoHabitacionController@editarPrecios');
		Route::get('reportes/financiero/anual', 'PropiedadController@reporteFinancieroAnual');
		Route::get('reportes/financiero', 'PropiedadController@reporteFinanciero');
		Route::get('obtener/pagos', 'PropiedadController@getPagos');
		Route::get('obtener/reserva', 'ReservaController@getPagoReserva');
		Route::get('secciones', 'RolController@getSecciones');
		Route::get('rol/permisos', 'RolController@getPermisos');
		Route::post('crear/usuario', 'UserController@crearUsuario');
		Route::get('estados', 'UserController@getEstados');

		Route::resource('user', 'UserController', ['except' => ['create', 'edit','store']]);
		Route::resource('propiedad', 'PropiedadController', ['except' => ['create', 'edit', 'store']]);
		Route::resource('habitacion', 'HabitacionController', ['except' => ['create', 'edit']]);
		Route::resource('servicio', 'ServicioController', ['except' => ['create', 'edit']]);
		Route::resource('reserva', 'ReservaController', ['except' => ['create', 'edit']]);
		Route::resource('cliente', 'ClienteController', ['except' => ['create', 'edit']]);
		Route::resource('huesped', 'HuespedController', ['except' => ['create', 'edit']]);
		Route::resource('temporada', 'TemporadaController', ['except' => ['create', 'edit']]);
		Route::resource('tipo/habitacion', 'TipoHabitacionController', ['except' => ['create', 'edit']]);

		Route::resource('rol', 'RolController', ['except' => ['create', 'edit']]);
});

});