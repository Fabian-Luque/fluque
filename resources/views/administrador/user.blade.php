@extends('administrador.home_admin')
@include('administrador.requests')
@section('container1')

@if(session('respuesta'))
    <?php 
        $resp = session('respuesta');
    ?>

    <script type="text/javascript">
        $(document).ready(
            function(e) {
                InfoModal(
                    "<?php echo $resp['accion'];?>",
                    "<?php echo $resp['msg'];?>"
                );
            }
        );
    </script>
@endif

<div id="respuesta" class="content">

    <div class="panel-group">
        <div class="panel-heading">
            <div class="card">
            <div class="btn-group btn-group-xs">
                <button type="button" id="btn-crear" class="btn btn-success">
  						<strong>CREAR</strong> 
                </button>
                <button type="button" id="btn-buscar" class="btn btn-info">BUSCAR</button>
                <button type="button" id="btn-editar" class="btn btn-warning">EDITAR</button>
                <button type="button" id="btn-eliminar" class="btn btn-danger">ELIMINAR</button>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">  
    	<div id="cont1" class="navbar-header">    
        </div>      
        <div class="card">
            <div class="content table-responsive table-full-width">
    			<table id="tablausuarios" class="table table-hover table-striped">
        			<thead>
            			<th>ID</th>
            			<th>NOMBRE</th>
            			<th>CORREO</th>
            			<th>TELEFONO</th>
            			<th>ACCIONES</th>
        			</thead>
        			<tbody>
        				@if(!empty($users[0]['id']))
        					@foreach($users as $user)
        						<tr data-id="{{ $user->id }}" >
									<td> {{ $user->id }} </td>
									<td> {{ $user->name }} </td>
									<td> {{ $user->email }} </td>
									<td> {{ $user->phone }} </td>
									<td> 
										<a href="u" name="b-lista" value="{{ $user->id }}" class="btn btn-info btn-xs">Editar</a>
										  
										<a href="d" name="b-lista" value="{{ $user->id }}" class="btn btn-danger disable btn-xs">Eliminar</a>	
									</td>
									@if(!empty($resp['errors']))
										<?php echo $resp; ?>
									@endif
   								</tr>
   							@endforeach
						@endif
        			</tbody>
    			</table>
			</div>
		</div>
    </div>
</div>
@endsection





