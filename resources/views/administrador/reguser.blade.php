@extends('administrador.home_admin')
@include('administrador.requests')
@section('registrar')
<div class="text-center col-md-4 col-md-offset-4" ">
  <div class="col-lg-12 col-offset-6 centered">
    <h3> Registro de Usuarios </h3>
  </div>
</div>  

<form class="container-fluid" name="f-crear-user" id="/dash/crear/user">
{{ csrf_field() }}

<div class="col-md-5" style="padding-left:130px; padding-bottom:10px; padding-top:30px;">
  <div class="form-group has-feedback">
    <label>nombre</label>
    <input type="text" class="form-control" name="name" >
    <span class="glyphicon glyphicon-user form-control-feedback"></span>
  </div>
  <div class="form-group has-feedback">
    <label>email</label>
    <input type="email" class="form-control" name="email" >
    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
  </div>
  <div class="form-group has-feedback">
    <label>password</label>
    <input type="password" class="form-control" name="password" >
    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
  </div>
  <div class="form-group has-feedback">
    <label>telefono</label>
    <input type="text" class="form-control" name="phone" >
    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
  </div>
  <div class="form-group has-feedback">
    <label>nombre propiedad</label>
    <input type="text" class="form-control" name="nombre" >
    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
  </div>
</div>
<div class="col-md-5" style="padding-left:130px; padding-bottom:10px; padding-top:30px;">                  

  <div class="form-group has-feedback">
    <label>Tipo propiedad</label>                     
    <select type="text" class="form-control" name="tipo_propiedad_id">
      @if(!empty($tprops[0]['id']))
        @foreach($tprops as $tprop)
          <option value="{{ $tprop->id }}">{{ $tprop->nombre }}</option>
        @endforeach
      @endif
    </select>
    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
  </div>
  <div class="form-group has-feedback">
    <label>Numero habitaciones</label>
    <input type="text" class="form-control" name="numero_habitaciones" >
    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
  </div>
  <div class="form-group has-feedback">
    <label>Ciudad</label>
    <input type="text" class="form-control" name="ciudad" >
    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
  </div>
  <div class="form-group has-feedback">
    <label>Direccion</label>
    <input type="text" class="form-control" name="direccion" >
    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
  </div>

<div class="col-md-5"> 

  
    <button type="submit" class="btn btn-primary btn-lg btn-block">registrar</button>
  

</div>
</form>



@endsection