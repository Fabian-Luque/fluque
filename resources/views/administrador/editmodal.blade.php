
<ul>
    @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
</ul>


{!! Form::open(['route' => array('crear.user', )]) !!}
<div class="container text-center" >   
  <div class="row">
    <div class="col-sm-2"">
      <div >
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
            'placeholder'=>'Telefono')) 
        !!}
      </div>
    
      </div>
    </div>
    <div class="col-sm-2">
      <div>
        <div class="form-group has-feedback">
          {!! Form::label('Tipo Propiedad') !!}
          <select type="text" class="form-control" name="tipo_propiedad_id">
            @if(!empty($tprops[0]['id']))
              @foreach($tprops as $tprop)
                <option value="{{ $tprop->id }}">{{ $tprop->nombre }}</option>
              @endforeach
            @endif
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
  </div>
</div>
<div class="form-group has-feedback" style="padding-left:3%; padding-right:35%; padding-top: 1%">
    {!! Form::submit(
      'Registrar', 
      array(
        'class'=>'btn btn-primary btn-lg btn-block'
      ))
    !!}
</div>
{!! Form::close() !!}