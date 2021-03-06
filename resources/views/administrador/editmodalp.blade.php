

{!! Form::open(['route' => array('editar.user', ), 'autocomplete' => 'off']) !!}
<div class="container text-center justify-content-center">   
  <div class="row">
    <div class="col-xs-2 col-sm-2 text-center">
      <div >
      {!! Form::hidden('id', '', array('id' => 'id_user')) !!}

      <div class="form-group has-feedback">
        {!! Form::label('Nombre Propiedad') !!}
        {!! Form::text('phone', null, 
          array(
            'required', 
            'class'=>'form-control',
            'name'=>'nombre', 
            'id'=>'nombre',
            'placeholder'=>'Nombre propiedad')) 
        !!}
      </div>

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
              'id' => 'num_hab',
              'class' => 'form-control',
              'name' => 'numero_habitaciones'
            ]) 
          !!}
        </div>

        <div class="form-group has-feedback">
          {!! Form::label('Latitud') !!}
          <input id="latitud" type="number" name="latitud" class="form-control" step="any" placeholder="Latitud Propiedad" required/>
        </div>
    
      </div>
    </div>
    <div class="col-xs-2 col-sm-2 text-center">
      <div>
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
          <select type="text" class="form-control" id="estado_cuenta" name="estado_cuenta_id">
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
          {!! Form::label('Longitud') !!}
          <input id="longitud" type="number" name="longitud" class="form-control" step="any" placeholder="Longitud Propiedad" required/>
        </div>

      </div>
    </div>
  </div>
  <div class="col-xs-4 col-sm-4 text-center">
  <div class="form-group has-feedback text-center" style="width: 250px;">
    {!! Form::submit(
      'Actualizar', 
      array(
        'class'=>'btn btn-primary btn-lg btn-block'
      ))
    !!}
</div>
</div>
</div>

{!! Form::close() !!}