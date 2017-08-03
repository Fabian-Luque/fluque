
@section('todos')
    <div class="container-fluid">  
        <div class="navbar-header">    
            <p class="navbar-brand"> Listado de Usuarios </p>  
        </div>      
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
                        @if( ! empty($user))
                            @foreach($users as $user) 
                                <td> {{ $user->id }} </td>
                                <td> {{ $user->name }} </td>
                                <td> {{ $user->email }} </td>
                                <td> {{ $user->phone }} </td>
                                <td> 
                                    <a href="{{ url('/problems') }}" class="btn btn-info btn-xs">Editar</a>

                                    <a href="{{ url('/problems') }}" class="btn btn-danger btn-xs">Eliminar</a> 
                                </td>
                            @endforeach
                        @endif
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection