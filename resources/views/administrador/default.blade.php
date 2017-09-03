<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>Laravel</title>
    {!! Html::style('assets/css/boostrap.css'); !!}
    {!! Html::style('assets/css/animate.min.css'); !!}
    {!! Html::style('assets/css/light-bootstrap-dashboard.css'); !!}
    {!! Html::style('assets/css/pe-icon-7-stroke.css'); !!}
    {!! Html::style('assets/css/boostrap.min.css'); !!}
</head>
<body>
    {!! HTML::script('assets/js/jquery-3.2.1.js'); !!}
    {!! HTML::script('assets/js/light-bootstrap-dashboard.js'); !!}
    {!! HTML::script('assets/js/bootstrap.min.js'); !!}
    @yield('scripts')
    @yield('content')
    @yield('resetmail')
    @yield('resetpass')
    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 id="titulomodal" class="modal-title"></h4>
                </div>
                <div id="textmodal" class="modal-body  justify-content-center" style="margin-left: 15%;" >
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