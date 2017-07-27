@extends('administrador.home_admin')
@section('container')
<div class="content">
    <div class="container-fluid">        
        <div class="card">
            <div class="content table-responsive table-full-width">
    			<table class="table table-hover table-striped">
        			<thead>
            			<th>ID</th>
            			<th>NOMBRE</th>
            			<th>CORREO</th>
            			<th>TELEFONO</th>
        			</thead>
        			<tbody>
        				<tr>
   							@foreach($users as $user) 
								<td> {{ $user->id }} </td>
								<td> {{ $user->name }} </td>
								<td> {{ $user->email }} </td>
								<td> {{ $user->phone }} </td>
							@endforeach
   						</tr>
        			</tbody>
    			</table>
			</div>
		</div>
    </div>
</div>
@endsection