<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>Laravel</title>
	<link href="{{ asset('assets/css/boostrap.css') }}" rel="stylesheet"> 
	<link href="{{ asset('assets/css/animate.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/css/light-bootstrap-dashboard.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/css/pe-icon-7-stroke.css') }}" rel="stylesheet"/>
</head>
<body>
	  <script type="text/javascript" src="{{ asset('assets/js/jquery-3.2.1.js') }}"></script> 
	
    <link href="{{ asset('assets/css/boostrap.min.css') }}" rel="stylesheet"> 
    <script type="text/javascript" src="{{ asset('assets/js/light-bootstrap-dashboard.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/bootstrap.min.js') }}"></script> 
@yield('content')
    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 id="titulomodal" class="modal-title"></h4>
                </div>
                <div id="textmodal" class="modal-body">
                </div>
                <div class="modal-footer">
                    <button id='confirma-del' value='' class='btn btn-danger'>Confirmar</button>
                    <button id="confirmamodal" type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>  
        </div>
    </div>
</body>
</html>