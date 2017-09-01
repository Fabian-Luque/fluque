@extends('administrador.default')
@section('content')
<div class="wrapper">
   <div class="sidebar" data-color="blue" data-image="/assets/img/mujer.jpg">
        <div class="sidebar-wrapper">
            <div class="logo">
                <a href="https://www.gofeels.com" class="simple-text">
                    <img class="img-responsive" src="https://www.gofeels.com/wp-content/uploads/2017/07/logodark.png">
                </a>
            </div>
            <ul class="nav">
                <li class="dropdown">
                    <a href="{{ url('/dash/adminuser') }}">
                        <i class="pe-7s-user"></i>
                        <p>Cuentas</p>
                    </a>
                </li> 
                <li>
                    <a href="{{ url('/dash/adminprop') }}">
                        <i class="pe-7s-note2"></i>
                        <p>Propiedades</p>
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
                        <span class="sr-only"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse" >
                    <ul class="nav navbar-nav navbar-left">       
                        <li>
                            <p class="navbar-brand">
                                Bienvenido!
                                {{ Auth::user()->name }} 
                            </p>
                        </li>
                    </ul>
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
        @yield('scripts')
        @yield('container1')
        @yield('registrar')
        @yield('acciones')
    </div>
</div>
@endsection