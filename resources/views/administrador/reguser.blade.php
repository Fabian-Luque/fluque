@extends('administrador.home_admin')
@include('administrador.requests')
@section('registrar')
{!! Html::style('assets/css/perfil_en_mapa.css'); !!}
<div class="text-center col-md-4 col-md-offset-4" ">
  <div class="col-lg-12 col-offset-6 centered">
    <h4> Registro de Cuenta </h4>
  </div>
</div>  

<ul>
    @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
</ul>

<script type="text/javascript">
  $(document).on(
      "unload",
      function () {
        $.ajax({
          type: "POST",
          url:  "<?php echo url('locate/prop'); ?>",
          headers: {
            'X-CSRF-TOKEN': "<?php echo csrf_token(); ?>"
          },
          data: { 
            _token: "<?php echo csrf_token(); ?>"
          },
                  
          success: function(data) {
            $( "#contenedor" ).append(data.html);
            $( "#contenedor" ).append(data.js);
            
          },
          error: function(xhr, textStatus, thrownError) {
                          
          }
        });
      }
  );

  $(document).ready(
      function () {
        $.ajax({
          type: "POST",
          url:  "<?php echo url('locate/prop'); ?>",
          headers: {
            'X-CSRF-TOKEN': "<?php echo csrf_token(); ?>"
          },
          data: { 
            _token: "<?php echo csrf_token(); ?>"
          },
                  
          success: function(data) {
            $( "#contenedor" ).append(data.html);
          },
          error: function(xhr, textStatus, thrownError) {
                          
          }
        });
      }
  );
</script>

<div id="contenedor">
{!! Form::open(['route' => array('crear.user', ), 'autocomplete' => 'off']) !!}
<center>
  <div class="container" style="padding-top: 6%;">   
  <div class="row" style="height: 100%;">
      
    <div class="col-lg-11 col-md-offset-1">
      <center>
            <div class="col-sm-3">
      <div>
      <div class="form-group has-feedback">
        {!! Form::label('Nombre') !!}
        {!! 
          Form::text(
            'nombre', 
            null, 
            array(
              'required', 
              'class'=>'form-control', 
              'name'=>'name',
              'placeholder'=>'Nombre'
            )
          ) 
        !!}
      </div>

      <div class="form-group has-feedback">
        {!! Form::label('Correo') !!}
        {!! Form::text(
          'email', 
          null, 
          array(
            'required', 
            'class'=>'form-control',
            'name'=>'email', 
            'id'=>'correo',
            'placeholder'=>'correo'
          )) 
        !!}
      </div>

      <div class="form-group has-feedback">
        {!! Form::label('password') !!}
        {!! Form::password(
          'password',[
            'class' => 'form-control', 
            'name'=>'password',
            'autocomplete'=>'new-password',
            'placeholder' => 'Password', 
            'type' => 'password'
          ]) 
        !!}
      </div>

      <div class="form-group has-feedback">
        {!! Form::label('Telefono') !!}
        {!! Form::text(
          'telefono', 
          null, 
          array(
            'required', 
            'class'=>'form-control', 
            'name'=>'phone',
            'placeholder'=>'Telefono'
          )) 
        !!}
      </div>

      <div class="form-group has-feedback">
        {!! Form::label('Nombre Propiedad') !!}
        {!! Form::text('phone', null, 
          array('required', 
            'class'=>'form-control',
            'name'=>'nombre', 
            'placeholder'=>'Nombre Propiedad')) 
        !!}
      </div>

      </div>
    </div>
    <div class="col-sm-3">
      <div >
        <div class="form-group has-feedback">
          {!! Form::label('Tipo Propiedad') !!}
          <select type="text" class="form-control" name="tipo_propiedad_id">
            <option value="1">
                  HOTEL
            </option>
            <option value="2">
                  HOSTAL
            </option>
          </select>
        </div>

        <div class="form-group has-feedback">
          {!! Form::label('Numero de Habitaciones') !!}
          {!! Form::number(
            'credit_amount', 
            '1', [
              'min' => '1', 
              'max' => '50000', 
              'class' => 'form-control',
              'name' => 'numero_habitaciones'
            ]) 
          !!}
        </div>

        <div class="form-group has-feedback">
          {!! Form::label('Ciudad') !!}
          {!! Form::text(
            'ciudad', 
            null, 
            array(
              'required', 
              'class'=>'form-control', 
              'name'=>'ciudad',
              'placeholder'=>'ciudad'
            )) 
          !!}
        </div>

        <div class="form-group has-feedback">
          {!! Form::label('Direccion') !!}
          {!! Form::text(
            'direccion', 
            null, 
            array(
              'required', 
              'class'=>'form-control', 
              'name'=>'direccion',
              'placeholder'=>'direccion'
            )) 
          !!}
        </div>

        
      </div>
    </div>

    <div class="col-sm-3">
      <div>
            <div class="form-group has-feedback">
          {!! Form::label('Tipo Cuenta') !!}
          <select type="text" class="form-control" name="tipo_cuenta">
                <option value="1">
                  prueba
                </option>
                <option value="2">
                  activa
                </option>
                <option value="3">
                  inactiva
                </option>
          </select>
        </div>

        <div class="form-group has-feedback">
          {!! Form::label('Periodo') !!}
          <select type="text" class="form-control" name="periodo">
                <option value="day">
                  Diario
                </option>
                <option value="week">
                  Semanal
                </option>
                <option value="month">
                  Mensual
                </option>
                <option value="year">
                  Anual
                </option>
          </select>
        </div>

        <div class="form-group has-feedback">
          {!! Form::label('Latitud') !!}
          <input type="number" name="latitud" class="form-control" step="any" placeholder="Latitud Propiedad" required/>
        </div>
        
        <div class="form-group has-feedback">
          {!! Form::label('Longitud') !!}
          <input type="number" name="longitud" class="form-control" step="any" placeholder="Longitud Propiedad" required/>
        </div>
      </div>
    </div>
      </center>
    </div>
  </div>
</div>
</center>
<div class="container-fluid" style="padding-right: 30%; padding-left: 30%;">
  <div class="row" style="margin-right:0;margin-left:0">
    <div class="row text-center">
      {!! Form::submit(
        'Registrar', 
        array(
          'class'=>'btn btn-primary btn-lg btn-block'
         ))
      !!}    
    </div>
  </div>
</div>
{!! Form::close() !!}
@endsection


</div>
