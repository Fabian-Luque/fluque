

{!! Form::open(['route' => array('editar.user', ), 'autocomplete' => 'off']) !!}
<div class="container text-center justify-content-center">   
  <div class="row">
    <div class="col-xs-4 col-sm-4 text-center">
      <div >
      {!! Form::hidden('id', '', array('id' => 'id_user')) !!}
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
              'id'=>'name',
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
            'id'=>'email', 
            'placeholder'=>'correo'
          )) 
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
            'id'=>'phone',
            'placeholder'=>'Telefono'
          )) 
        !!}
      </div>
      </div>
    </div>
  </div>
</div>
<div class="form-group has-feedback" style="padding-left:3%; padding-right:35%; padding-top: 1%">
    {!! Form::submit(
      'Actualizar', 
      array(
        'class'=>'btn btn-primary btn-lg btn-block'
      ))
    !!}
</div>
{!! Form::close() !!}