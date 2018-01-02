@extends('administrador.default')
@include('administrador.requests')

@section('resetmail')
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
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Cambiar contraseña</div>
                <div class="panel-body">
		{!! Form::open(['route' => array('reset.pass.sendmail', ), 'autocomplete' => 'off']) !!}
				<div class="form-group">
					<label for="email" class="cols-sm-2 control-label">Tu correo</label>
					<div class="cols-sm-10">
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-envelope fa" aria-hidden="true"></i></span>
							
        					{!! Form::text(
          						'destino', 
          						null, 
          						array(
            						'required', 
            						'class'=>'form-control',
            						'name'=>'destino', 
            						'id'=>'destino',
            						'placeholder'=>'Ingresa el correo'
          						)
          					)!!}
						</div>
						<small class="text-muted">
							Para restablecer tu contraseña, ingresa el correo asociado a tu cuenta
						</small>
					</div>
				</div>
				<div class="form-group ">
				    {!! Form::submit(
      					'Enviar', 
      					array(
        					'class'=>'btn btn-primary btn-lg btn-block'
      					)
      				)!!}
				</div>
				<!--
				<div class="login-register">
			    	<a href="index.php">Login</a>
				</div>
				-->
			{!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
</div>

@endsection