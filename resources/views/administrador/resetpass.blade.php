@extends('administrador.default')
@include('administrador.requests')
@section('resetpass')
<style type="text/css">
    body {
      background-color: #494a6b;
      padding-top: 10%;  
    }
</style>
@if(session('respuesta'))
    <?php 
        $resp = session('respuesta');
    ?>
    <script type="text/javascript">
        $(document).ready(

            function(e) {
                InfoModal(
                    "Respuesta",
                    "<?php echo $resp['msg'];?>"
                );
            }
        );
    </script>
@endif

<div class="container">
    <div id="passwordreset" style="margin-top:50px" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
      <div class="panel panel-info">
                    <div class="panel-heading">
                        <div class="panel-title">Cambiar Contraseña</div>
                    </div>                     
                    <div class="panel-body">
                        {!! Form::open(['route' => array('cambiar.pass', ), 'autocomplete' => 'off', 'class' => 'form-horizontal']) !!}

                          @if(session('token_reset'))
                            <?php 
                              $token_reset = session('token_reset');
                            ?>

                            {{Form::hidden(
                              'token_reset', 
                              $token_reset, 
                              array(
                                'id' => 'token_reset_id',
                                'name'=> 'token_reset'
                            ))}}

                          @elseif(session('respuesta'))
                            <?php 
                              $respuesta = session('respuesta');
                            ?>

                            {{Form::hidden(
                              'token_reset', 
                              $respuesta['tok'], 
                              array(
                                'id' => 'token_reset_id',
                                'name'=> 'token_reset'
                            ))}}
                          @endif
                            <div class="form-group">
                                <label for="email" class=" control-label col-sm-3">Correo de la cuenta</label>
                                <div class="col-sm-9"> 
        						{!! Form::text(
          							'email', 
          							null, 
          							array(
            							'required', 
            							'class'=>'form-control',
            							'name'=>'email', 
            							'id'=>'correo',
            							'placeholder'=>'Correo de la cuenta'
          							)
          						)!!}
          						</div>
                            </div>
                            <div class="form-group">
                                <label for="email" class=" control-label col-sm-3">Nueva contraseña</label>
                                <div class="col-sm-9">
                                {!! Form::password(
          							'password',[
            						'class' => 'form-control', 
            						'name'=>'password',
            						'autocomplete'=>'new-password',
            						'placeholder' => 'Nueva contraseña', 
            						'type' => 'password'
          						])!!}
          						</div>
                            </div>
                            <div class="form-group">

                                <label for="email" class=" control-label col-sm-3">Confirmacion</label>
                                <div class="col-sm-9">
                                {!! Form::password(
          							'passwordc',[
            						'class' => 'form-control', 
            						'name'=>'passwordc',
            						'autocomplete'=>'new-password',
            						'placeholder' => 'Confirme contraseña', 
            						'type' => 'password'
          						])!!}
          						</div>
                            </div>
                            <div class="form-group">             
                                <div class="  col-sm-offset-3 col-sm-9">
                                    {!! Form::submit(
      									'Cambiar', 
      									array(
        									'class'=>'btn btn-primary btn-lg btn-block'
      									)
      								)!!}
                                </div>
                            </div>                             
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>             
        </div>
@endsection