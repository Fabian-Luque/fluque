@extends('administrador.default')

@section('content')
<div class="wrapper">
    <div class="sidebar" data-color="purple" data-image="assets/img/sidebar-5.jpg">

        <div class="sidebar-wrapper">
            <div class="logo">
                <a href="https://holajarvis.com" class="simple-text">
                    Jarvis Admin
                </a>
            </div>

            <ul class="nav">
            <!--
                <li class="active">
                    <a href="dashboard.html">
                        <i class="pe-7s-graph"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
            -->
                <li>
                    <a href="{{ url('/adminuser') }}">
                        <i class="pe-7s-user"></i>
                        <p>Usuarios</p>
                    </a>
                </li>
                <li>
                    <a href="table.html">
                        <i class="pe-7s-note2"></i>
                        <p>Table List</p>
                    </a>
                </li>
            
            </ul>
        </div>
    </div>

    <div class="main-panel">
        <nav class="navbar navbar-default navbar-fixed">
            <div class="container-fluid">
                <div class="navbar-header">
                    
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navigation-example-2">

                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar">hola1</span>
                        <span class="icon-bar">hola2</span>
                        <span class="icon-bar">hola3</span>
                    </button>
                </div>

                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-right">             
                        <li>
                            <a href="{{ url('/logout') }}">
                                Cerrar sesion 
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        @yield('container')
        <footer class="footer">
            <div class="container-fluid">
                <nav class="pull-left">
                  
                </nav>
            </div>
        </footer>
    </div>
</div>
@endsection
