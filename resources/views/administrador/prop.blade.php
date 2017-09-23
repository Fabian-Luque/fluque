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
    <h4 class="text-center"> Informacion de Propiedades </h4>
  
<div id="respuesta" class="content">
    <div class="container-fluid">  
    	<div id="cont1" class="navbar-header">    
        </div>      
        <div class="card">
            <div class="table-responsive">
    			<table id="tablausuarios" class="table table-striped table-hover">
        			<thead>
            			<th>ID</th>
            			<th>NOMBRE</th>
                        <th>TIPO</th>
            			<th>NUMERO DE HAB</th>
            			<th>CIUDAD</th>
            			<th>DIRECCION</th>
            			<th>CUENTA</th>
                        <th>CREADO</th>
        			</thead>
        			<tbody>
        				@if(!empty($props[0]['id']))
        					@foreach($props as $prop)
        						<tr data-id="{{ $prop->id }}" >
									<td> {{ $prop->id }} </td>
									<td> {{ $prop->nombre }} </td>
                                    <td> {{ $prop->tipo_propiedad }} </td>
									<td> {{ $prop->numero_habitaciones }} </td>
									<td> {{ $prop->ciudad }} </td>
									<td> {{ $prop->direccion }} </td>
                                    <td> {{ $prop->estado_cuenta }} </td>
                                    <td> {{ $prop->created }} </td>
                                    <td> 
                                        <a href="upr" name="b-lista" value="{{ $prop->id }}" class="btn btn-info btn-xs">Editar</a>
           
                                    </td>
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
