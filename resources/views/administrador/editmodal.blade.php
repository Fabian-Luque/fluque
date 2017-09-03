
<ul>
    @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
</ul>

{!! Form::open(['route' => array('editar.user', ), 'autocomplete' => 'off']) !!}
<div class="container text-center justify-content-center">   
  <div class="row">
    <div class="col-xs-2 col-sm-2 text-center">
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
        {!! Form::label('password') !!}
        {!! Form::password(
          'password',[
            'class' => 'form-control', 
            'name'=>'password',
            'id'=>'password',
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
            'id'=>'phone',
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
            'id'=>'nombre',
            'placeholder'=>'Telefono')) 
        !!}
      </div>
    
      </div>
    </div>
    <div class="col-xs-2 col-sm-2 text-center">
      <div>
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
          {!! Form::label('N° Habitaciones') !!}
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
              'id'=>'ciudad',
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
              'id'=>'direccion',
              'name'=>'direccion',
              'placeholder'=>'direccion'
            )) 
          !!}
        </div>

        <div class="form-group has-feedback">
          {!! Form::label('Tipo Cuenta') !!}
          <select type="text" class="form-control" name="tipo_cuenta">
                <option value="0">
                  prueba
                </option>
                <option value="1">
                  activa
                </option>
                <option value="2">
                  inactiva
                </option>
          </select>
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