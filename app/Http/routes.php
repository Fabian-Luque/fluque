<?php

use Illuminate\Http\Response as HttpResponse;
use App\Events\ReservasMotorEvent;
use App\User;

Route::auth();

Route::get(
  '/', 
  function () {
    return view('auth.login');
  }
);

Route::post('propiedad/cercana/obtener','GeoController@PropiedadesCercanas');


Route::post('guardar/ubicacion/propiedad', 'GeoController@UbicacionCreate');
Route::post('googlemaps', 'GeoController@GoogleMaps');

Route::post('mapjs', 'GeoController@GoogleMaps');
Route::post('locate/prop', 'GeoController@AddLocatePropiedad');


Route::get('hotelescercanos', 'GeoController@Gmaps');
Route::post('hoteles/cercanos', 'GeoController@Gmaps');


Route::post('ejmm', 'CorreoController@SendFileByEmail');


Route::get(
  '/upload', 
  function () {
    return view('correos.testimg');
  }
);

Route::get(
  '/gmap', 
  function () {
    return view('administrador.gmap');
  }
);

Route::get(
  '/p', 
  function () {
    return view('administrador.prueba');
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
      'edituserp', 
      function() {
          return View::make('administrador.editmodalp');
      }
    );

    Route::get(
      'buscauser', 
      function() {
          return View::make('administrador.accionesC');
      }
    );
    
    Route::get('adminuser', 'DashControllers\UserDashController@ReadUser');
        
    

    Route::post('actualizar/user', 'DashControllers\UserDashController@UpdateUser')->name('editar.user');
    Route::post('obtener/user', 'DashControllers\UserDashController@ReadUser');
    Route::post('eliminar/user', 'DashControllers\UserDashController@DeleteUser');
  }
);
//////////////////////// rutas dash  ///////////////////////////////////////





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

//// motor de reserva
Route::get('motor/reserva', 'MotorWidgetControllers\MotorController@getMotor');
Route::get('motor/disponibilidad', 'MotorWidgetControllers\MotorController@getDisponibilidad');
Route::get('motor/disponibilidad/habitacion', 'MotorRaController@getDisponibilidad');
Route::post('motor/reserva/habitacion', 'MotorRaController@reserva');
Route::get('crear/codigo', 'PropiedadController@crearCodigo');
Route::get('tipo/cliente/motor', 'ClienteController@getTipoCliente');
Route::get('paises/motor', 'PropiedadController@getPaises');
Route::get('regiones/motor', 'PropiedadController@getRegiones');
Route::get('cantidad/tipo/habitacion', 'TipoHabitacionController@cantidadTipoHabitacion');
Route::get('cliente/motor', 'ClienteController@index');
Route::put('cliente/motor/{id}', 'ClienteController@update');
Route::get('obtener/colores', 'MotorRaController@getColoresPropiedad');


Route::get('comprobar/{correo}/{retorno}/{token}', 'RegistroController@comprobar'); // paso 2
Route::get('reset/password/{token}', 'ApiAuthController@ResetPassword');

Route::group(['as' => 'api.jarvis.'], function() {
	Route::post('registro', 'UserController@store');
	Route::post('signin', 'ApiAuthController@signin');
	
	Route::post('/signup', 'RegistroController@signup'); // paso 1
	Route::post('/signin2', 'RegistroController@signin'); // paso 3
	Route::post('/configurar', 'RegistroController@configurar'); // paso 4
	Route::post('/temporadas', 'RegistroController@calendario'); // paso 5
	Route::post('/habitaciones', 'RegistroController@habitaciones'); // paso 6
	Route::post('/stripe', 'RegistroController@stripe'); // paso 7
	Route::post('/configuracion/obtener', 'RegistroController@Getconfig');
	Route::post('/paso/modificar', 'RegistroController@SetEstado');
	Route::post('tipo-habitacion/obtener', 'RegistroController@GetTiposHabitacion');


	Route::group(['middleware' => ['jwt.auth']], function () {
		Route::post('cuentas/crear', 'DashControllers\UserDashController@CreateUser');
		Route::post('cuentas/obtener', 'DashControllers\UserDashController@getUsers');
		Route::post('propiedades/obtener', 'DashControllers\UserDashController@getProps');
		Route::post('cuentas/actualizar', 'DashControllers\UserDashController@UpdateCuenta');
		//Route::post('propiedades/obtener', 'DashControllers\UserDashController@getViewPropiedad');

		Route::post('upload/images', 'S3Controller@UploadImage');
		Route::post('delete/images', 'S3Controller@DeleteImage');
		Route::post('delete/directory', 'S3Controller@DeleteDirectory');
		Route::post('update/image', 'S3Controller@UpdateImage');
		Route::post('update/directory', 'S3Controller@UpdateNameDirectory');
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
		Route::post('pdf/estado/cuenta/resumen', 'PDFController@estadoCuentaResumen');
		Route::post('pdf/estado/cuenta', 'PDFController@estadoCuenta');
		Route::post('pdf/reporte', 'PDFController@reporte');
		Route::post('pdf/reporte/financiero', 'PDFController@reporteFinanciero');
		Route::post('pdf/entradas', 'PDFController@entradas');
		Route::post('pdf/salidas', 'PDFController@salidas');
		Route::post('pdf/huesped', 'PDFController@huesped');
		Route::post('pdf/checkin', 'PDFController@checkin');
		Route::post('pdf/pagos', 'PDFController@pagos');
		Route::post('pdf/reservas', 'PDFController@reservas');
		Route::post('pdf/comprobante/reserva', 'PDFController@comprobanteReserva');
		Route::post('pdf/comprobante/reserva/resumen', 'PDFController@comprobanteReservaResumen');
		Route::post('pdf/caja', 'PDFController@caja');
		Route::post('ingreso/servicio', 'PropiedadController@ingresoServicio');
		Route::post('ingreso/servicio/cliente', 'ClienteController@ingresoServicio');
		Route::get('cliente/empresa', 'ClienteController@getClientes');
		Route::post('pago/consumo', 'ReservaController@pagoConsumo');
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
		Route::get('pernoctacion/tipo/habitacion', 'PropiedadController@pernoctacionTipoHabitacion');
		Route::get('paises', 'PropiedadController@getPaises');
		Route::get('regiones', 'PropiedadController@getRegiones');
		Route::post('calendario/temporada', 'TemporadaController@calendario');
		Route::post('precio/temporada', 'HabitacionController@temporada');
		Route::get('precio/habitacion', 'HabitacionController@precioHabitacion');
		Route::get('periodo/calendario', 'TemporadaController@getCalendario');
		Route::post('eliminar/calendario', 'TemporadaController@eliminarCalendario');
		Route::get('temporada/precios', 'TemporadaController@getPreciosTemporadas');
		Route::post('editar/temporadas', 'TemporadaController@editarTemporadas');
		Route::get('reportes', 'PropiedadController@reporteGeneral');
		Route::post('reportes/pago', 'PropiedadController@pagos');
		Route::get('zonas/horarias', 'PropiedadController@getZonasHorarias');
		Route::put('pago/{id}', 'ReservaController@editarPago');
		Route::delete('pago/{id}', 'ReservaController@eliminarPago');
		Route::post('reserva/busqueda', 'ReservaController@filtroReservas');
		Route::get('tipo/cobros', 'PropiedadController@getTipoCobro');
		Route::post('editar/precios', 'TipoHabitacionController@editarPrecios');
		Route::get('reportes/financiero/anual', 'PropiedadController@reporteFinancieroAnual');
		Route::get('reportes/egresos/anual', 'PropiedadController@reporteEgresoAnual');
		Route::get('reportes/egresos', 'PropiedadController@reporteEgresos');
		Route::get('reportes/financiero', 'PropiedadController@reporteFinanciero');
		Route::get('obtener/pagos', 'PropiedadController@getPagos');
		Route::get('obtener/reserva', 'ReservaController@getPagoReserva');
		Route::get('secciones', 'RolController@getSecciones');
		Route::get('rol/permisos', 'RolController@getPermisos');
		Route::post('crear/usuario', 'UserController@crearUsuario');
		Route::get('estados', 'UserController@getEstados');
		Route::post('abrir/caja', 'CajaController@abrirCaja');
		Route::post('cerrar/caja', 'CajaController@cerrarCaja');
		Route::get('tipo-monto', 'CajaController@tipoMonto');
		Route::get('caja/abierta', 'CajaController@getCajaAbierta');
		Route::post('ingresar/egreso/caja', 'EgresoController@ingresarEgresoCaja');
		Route::post('ingresar/egreso/propiedad', 'EgresoController@ingresarEgresoPropiedad');
		Route::get('reportes/cajas', 'CajaController@getCajas');
		Route::get('obtener/caja', 'CajaController@getCaja');
		Route::get('obtener/egresos/caja', 'EgresoController@obtenerEgresosCaja');
		Route::get('obtener/egresos/propiedad', 'EgresoController@obtenerEgresosPropiedad');
		Route::put('editar/egreso/caja/{id}', 'EgresoController@editarEgresoCaja');
		Route::put('editar/egreso/propiedad/{id}', 'EgresoController@editarEgresoPropiedad');
		Route::post('crear/politicas', 'PropiedadController@crearPoliticas');
		Route::delete('egreso/caja/{id}', 'EgresoController@eliminarEgresoCaja');
		Route::delete('egreso/propiedad/{id}', 'EgresoController@eliminarEgresoPropiedad');
		Route::put('editar/politica/{id}', 'PropiedadController@editarPolitica');
		Route::delete('eliminar/politica/{id}', 'PropiedadController@eliminarPolitica');
		Route::get('habitaciones/disponibles', 'MotorRaController@habitacionesDisponibles');
		Route::get('obtener/reservas/motor', 'MotorRaController@getReservasMotor');
		Route::post('asignar/habitacion', 'MotorRaController@asignarHabitacion');


		Route::post('crear/cuenta/bancaria', 'PropiedadController@crearCuentaBancaria');
		Route::put('editar/cuenta/bancaria/{id}', 'PropiedadController@editarCuentaBancaria');
		Route::delete('eliminar/cuenta/bancaria/{id}', 'PropiedadController@eliminarCuentaBancaria');
		Route::get('tipo/cuenta', 'PropiedadController@getTipoCuenta');
		Route::post('tipo/deposito/propiedad', 'PropiedadController@crearTipoDepositoPropiedad');
		Route::put('tipo/deposito/propiedad/{id}', 'PropiedadController@editarTipoDepositoPropiedad');
		Route::delete('tipo/deposito/propiedad/{id}', 'PropiedadController@eliminarTipoDepositoPropiedad');
		Route::get('tipo/deposito', 'PropiedadController@getTipoDeposito');
		Route::get('colores/motor', 'MotorRaController@getColores');
		Route::get('clasificacion/color', 'MotorRaController@getClasificacionColores');
		Route::post('asignar/color/motor', 'MotorRaController@asignarColorMotor');
		Route::post('editar/color/motor', 'MotorRaController@editarColor');
		Route::post('anular/reservas', 'ReservaController@anularReservas');
		Route::post('editar/tipo/habitacion', 'TipoHabitacionController@editarTipoHabitacion');
		Route::post('habitaciones/disponibles/reserva', 'ReservaController@habitacionesDisponibles');
		Route::post('cambiar/fechas/reserva', 'ReservaController@cambiarFechasReserva');
		Route::get('reservas/credito', 'ReservaController@getCuentasCredito');
		Route::post('confirmar/pago', 'ReservaController@confirmarPagoReserva');
		Route::post('obtener/reservas/cliente', 'MotorRaController@getReservasCliente');
		Route::post('obtener/consumos/particulares', 'PropiedadController@getConsumosParticulares');
		Route::post('editar/consumos/particulares', 'PropiedadController@editarConsumoParticulares');
		Route::post('eliminar/consumos/particulares', 'PropiedadController@eliminarConsumosParticulares');


		// rutas mapa geolozalizacion 
		Route::get('mapa/disponibilidad/habitacion', 'ReservaMapaController@getDisponibilidad');
		Route::post('mapa/reserva/habitacion', 'ReservaMapaController@reserva');

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
		Route::resource('egreso', 'EgresoController', ['except' => ['create', 'edit']]);
});

});

    Route::get('crear/permisos', 'RolController@crearPermisos');


    Route::post('crear/zona/horaria', 'PropiedadController@crearZona');
    Route::post('crear/pais', 'PropiedadController@crearPais');

Route::post('evento', 'DashControllers\UserDashController@evento');






Route::post('cliente/stripe/crear','StripeController@ClienteStripeCrear');
Route::post('plan/stripe/crear','StripeController@PlanStripeCrear');
Route::post('subscripcion/stripe/crear','StripeController@SubscripcionStripeCrear');

Route::post('tarjeta/stripe/crear','StripeController@tarjetaStripeCrear');
Route::post('tarjeta/stripe/obtener','StripeController@tarjetaStripeObtener');
Route::post('tarjeta/stripe/actualizar','StripeController@tarjetaStripeActualizar');
Route::post('tarjeta/stripe/eliminar','StripeController@tarjetaStripeEliminar');

Route::post('invoice/stripe/obtener','StripeController@InvoiceStripeObtener');





Route::post('myallocator/configurar', 'MyallocatorController@Configuracion');




Route::post('get/images', 'S3Controller@GetImage');
Route::post('get/images/byfolder', 'S3Controller@GetAllImagesByDir');







Route::post('mensaje/enviar', 'ChatController@SendMessage');
Route::post('mensaje/obtener', 'ChatController@GetAllMessages');
Route::post('conversacion/obtener', 'ChatController@GetConversacion');

Route::post('mensaje/noleido', 'ChatController@ConvNoLeidas');

Route::post('mensaje/obtener/ultimos', 'ChatController@GetMessagesByReceptor');
Route::post('mensaje/estado', 'ChatController@EstadoMensaje');



Route::post('pdf/comprobante/reserva/resumen2', 'PDFController@comprobanteReservaResumen');



Route::post('asignar/prueba', 'MotorRaController@prueba');

Route::post('correo', 'PDFController@envm');




