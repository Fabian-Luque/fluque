<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Laravel</title>

	<link href="{{ asset('assets/css/boostrap.css') }}" rel="stylesheet"> 

	<link href="{{ asset('assets/css/boostrap.min.css') }}" rel="stylesheet"> 

	<link href="{{ asset('assets/css/animate.min.css') }}" rel="stylesheet"/>
    <!--  Light Bootstrap Table core CSS    -->
    <link href="{{ asset('assets/css/light-bootstrap-dashboard.css') }}" rel="stylesheet"/>

	<!-- Fonts -->
	<link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>

</head>
<body>

	@yield('content')

	<!-- Scripts -->
	<script type="text/javascript" src="{{ asset('assets/js/bootstrap.min.js') }}"></script> 
</body>
</html>
