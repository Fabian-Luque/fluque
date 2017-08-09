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
            			<th>NUMERO DE HAB</th>
            			<th>CIUDAD</th>
            			<th>DIRECCION</th>
            			<th>TELEFONO</th>
            			<th>CORREO</th>
            			<th>RESPONSABLE</th>
            			<th>DESCRIPCION</th>
            			<th>IVA</th>
            			<th>% DEPOSITO</th>
            			<th>PAIS</th>
            			<th>REGION</th>
            			<th>PROPIEDAD</th>
            			<th>USUARIO</th>
            			<th>TIPO</th>
            			<th>ACCION</th>
        			</thead>
        			<tbody>
        				@if(!empty($props[0]['id']))
        					@foreach($props as $prop)
        						<tr data-id="{{ $prop->id }}" >
									<td> {{ $prop->id }} </td>
									<td> {{ $prop->nombre }} </td>
									<td> {{ $prop->numero_habitaciones }} </td>
									<td> {{ $prop->ciudad }} </td>
									<td> {{ $prop->direccion }} </td>
									<td> {{ $prop->telefono }} </td>
									<td> {{ $prop->email }} </td>
									<td> {{ $prop->nombre_responsable }} </td>
									<td> {{ $prop->descripcion }} </td>
									<td> {{ $prop->iva }} </td>
									<td> {{ $prop->porcentaje_deposito }} </td>
									
									<td> {{ $prop->pais_id }} </td>
									<td> {{ $prop->region_id }} </td>
									<td> {{ $prop->prop_id }} </td>
									<td> {{ $prop->user_id }} </td>
									<td> {{ $prop->tipo_propiedad_id }} </td>
									<td> 
										<a href="u" name="b-lista" value="{{ $prop->id }}" class="btn btn-info btn-xs">Editar</a>
										  
										<a href="d" name="b-lista" value="{{ $prop->id }}" class="btn btn-danger disable btn-xs">Eliminar</a>	
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
