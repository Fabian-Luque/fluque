@extends('layouts.app2')
@include('administrador.requests')

@section('resetmail')
<ul>
    @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
</ul>

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

<div class="container" style="padding-left: 30%; padding-right: 30%;">
	<div class="row main">
		<div class="panel-heading">
	        <div class="panel-title text-center">
	            <h1 class="title">GoFeels</h1>
	            <hr/>
	        </div>
	    </div> 
		<div class="main-login main-center">
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
@endsection