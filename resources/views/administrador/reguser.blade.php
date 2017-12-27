@extends('administrador.home_admin')
@include('administrador.requests')

<script type="text/javascript">
	var map;
</script>

<style>
/* The Modal (background) */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    padding-top: 50px; /* Location of the box */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
    position: relative;
    background-color: #fefefe;
    margin: auto;
    padding: 0;
    border: 1px solid #888;
    width: 80%;
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
    -webkit-animation-name: animatetop;
    -webkit-animation-duration: 0.4s;
    animation-name: animatetop;
    animation-duration: 0.4s
}

/* Add Animation */
@-webkit-keyframes animatetop {
    from {top:-300px; opacity:0} 
    to {top:0; opacity:1}
}

@keyframes animatetop {
    from {top:-300px; opacity:0}
    to {top:0; opacity:1}
}

/* The Close Button */
.close {
    color: white;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}

.modal-header {
    padding: 2px 2px;
    background-color: #fafafa;
    color: black;
}

.modal-body {padding: 2px 16px;}

.modal-footer {
    padding: 2px 5px;
    background-color: #fafafa;
    color: white;
}

#map {
        height: 400px;
      }
      /* Optional: Makes the sample page fill the window. */
      .controls {
        margin-top: 10px;
        border: 1px solid transparent;
        border-radius: 2px 0 0 2px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        height: 32px;
        outline: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
      }

      #pac-input {
        background-color: #fff;
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
        margin-left: 12px;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 300px;
      }

      #pac-input:focus {
        border-color: #4d90fe;
      }

      .pac-container {
        font-family: Roboto;
      }

      #type-selector {
        color: #fff;
        background-color: #4d90fe;
        padding: 5px 11px 0px 11px;
      }

      #type-selector label {
        font-family: Roboto;
        font-size: 13px;
        font-weight: 300;
      }
      #target {
        width: 345px;
      }
</style>



@section('registrar')
<script type="text/javascript">
	$(document).ready(
		function () {
			$('#myForm').submit(
				function() {
    				$('input[name="direc_go"]').prop(
    					'disabled', 
    					true
    				);
				}
			);
		}
	);
</script>
<ul>
	@foreach($errors->all() as $error)
		<li>{{ $error }}</li>
	@endforeach
</ul>

<div id="contenedor">


</div>
{!! Form::open(['route' => array('crear.user', ), 'autocomplete' => 'off']) !!}

<form id="myForm" method="POST" action="{{ url('/dash/crear/user') }}" accept-charset="UTF-8" autocomplete="off">

	{{ csrf_field() }}

<center>
  <div class="container" style="padding-top: 6%;">   
  <div class="row" style="height: 100%;">
	  
	<div class="col-lg-11 col-md-offset-1">
	  <center>
			<div class="col-sm-3">
	  <div>
	  <div class="form-group has-feedback">
		{!! Form::label('Nombre') !!}
		{!! 
		  Form::text(
			'nombre', 
			null, 
			array(
			  'required', 
			  'class'=>'form-control', 
			  'name'=>'name',
			  'placeholder'=>'Nombre'
			)
		  ) 
		!!}
	  </div>

	  <div class="form-group has-feedback">
		{!! Form::label('Correo') !!}
		{!! Form::text(
		  'email', 
		  null, 
		  array(
			'required', 
			'class'=>'form-control',
			'name'=>'email', 
			'id'=>'correo',
			'placeholder'=>'correo'
		  )) 
		!!}
	  </div>

	  <div class="form-group has-feedback">
		{!! Form::label('password') !!}
		{!! Form::password(
		  'password',[
			'class' => 'form-control', 
			'name'=>'password',
			'autocomplete'=>'new-password',
			'placeholder' => 'Password', 
			'type' => 'password'
		  ]) 
		!!}
	  </div>

	  <div class="form-group has-feedback">
		{!! Form::label('Telefono') !!}
		{!! Form::text(
		  'telefono', 
		  null, 
		  array(
			'required', 
			'class'=>'form-control', 
			'name'=>'phone',
			'placeholder'=>'Telefono'
		  )) 
		!!}
	  </div>

	  <div class="form-group has-feedback">
		{!! Form::label('Nombre Propiedad') !!}
		{!! Form::text('phone', null, 
		  array('required', 
			'class'=>'form-control',
			'name'=>'nombre', 
			'placeholder'=>'Nombre Propiedad')) 
		!!}
	  </div>

	  </div>
	</div>
	<div class="col-sm-3">
	  <div >
		<div class="form-group has-feedback">
		  {!! Form::label('Tipo Propiedad') !!}
		  <select type="text" class="form-control" name="tipo_propiedad_id">
			<option value="1">
				  HOTEL
			</option>
			<option value="2">
				  HOSTAL
			</option>
		  </select>
		</div>

		<div class="form-group has-feedback">
		  {!! Form::label('Numero de Habitaciones') !!}
		  {!! Form::number(
			'credit_amount', 
			'1', [
			  'min' => '1', 
			  'max' => '50000', 
			  'class' => 'form-control',
			  'name' => 'numero_habitaciones'
			]) 
		  !!}
		</div>

		<div class="form-group has-feedback">
		  {!! Form::label('Ciudad') !!}
		  {!! Form::text(
			'ciudad', 
			null, 
			array(
			  'required', 
			  'class'=>'form-control', 
			  'name'=>'ciudad',
			  'placeholder'=>'ciudad'
			)) 
		  !!}
		</div>

		<div class="form-group has-feedback">
		  {!! Form::label('Direccion') !!}
		  {!! Form::text(
			'direccion', 
			null, 
			array(
			  'required', 
			  'class'=>'form-control', 
			  'name'=>'direccion',
			  'placeholder'=>'direccion'
			)) 
		  !!}
		</div>  

		<div class="form-group has-feedback">
		  {!! Form::label('Localizacion Geografica') !!}
		  <button type="button" class="btn btn-primary btn-block" id="myBtn">
		  	buscar
		  </button>
		</div>  

<!-- The Modal -->
<div id="myModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <span class="close">&times;</span>
      <h3>Ubicacion Propiedad</h3>
    </div>
    <div class="modal-body">
      	<input id="pac-input" name="direc_go" class="controls"  placeholder="Ingrese direccion">
		<div id="map"></div>
    </div>
    <div class="modal-footer">
    </div>
  </div>

</div>

<script>
	var marker;

	function checkContainer () {
		if($('#myModal').is(':visible')) { 
		    google.maps.event.trigger(map, "resize");
		} else {
		   setTimeout(checkContainer, 50); 
	  	}
	}

	function mark_drag(event) {
		$("#latitud").val(event.latLng.lat());
		$("#longitud").val(event.latLng.lng());  		
	}

  	function initAutocomplete() {
	    map = new google.maps.Map(
	    	document.getElementById('map'), {
	      	center: {lat: -33.8688, lng: 151.2195},
	      	zoom: 13,
	      	mapTypeId: 'roadmap'
	    });

	    var input = document.getElementById('pac-input');
	    var searchBox = new google.maps.places.SearchBox(input);
	    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

	    map.addListener(
	    	'bounds_changed', 
	    	function() {
	      		searchBox.setBounds(map.getBounds());
	    	}
	    );

	    searchBox.addListener('places_changed', function() {
	      var places = searchBox.getPlaces();

	      if (places.length == 0) {
	        return;
	      }

	      var bounds = new google.maps.LatLngBounds();
	      places.forEach(
	      	function(place) {
	        	if (!place.geometry) {
	          		console.log("Returned place contains no geometry");
	          		return;
	        	}

	        	marker = new google.maps.Marker({
          		position: place.geometry.location,
          		map: map, 
          		draggable: true,
          		//icon: icon,
          		animation: google.maps.Animation.DROP,
          		title: place.name
        	});

	       	google.maps.event.addListener(
	       		marker, 
	       		'drag', 
	       		function(event) {
		  			mark_drag(event); 
				}
			);

			//marker.addListener('click', toggleBounce);

			google.maps.event.addDomListener(
				window, 'load', initAutocomplete
			);

	        if (place.geometry.viewport) {
	          // Only geocodes have viewport.
	          bounds.union(place.geometry.viewport);
	        } else {
	          bounds.extend(place.geometry.location);
	        }
	      });
	      map.fitBounds(bounds);
	    });
  	}

  	jQuery(document).ready(
		function() {
  			checkContainer();

  			$("#pac-input").keypress(
  				function( event ) {
  					if (event.which == 13) {
  						marker.setMap(null);
  					}
				}
			);

			$("#pac-input").change(
				function() {
					marker.setMap(null);
				}
			);
		}
	);
</script>

<script>
// Get the modal
var modal = document.getElementById('myModal');

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
btn.onclick = function() {
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>

	  </div>
	</div>

	<div class="col-sm-3">
	  <div>
			<div class="form-group has-feedback">
		  {!! Form::label('Tipo Cuenta') !!}
		  <select type="text" class="form-control" name="tipo_cuenta">
				<option value="1">
				  prueba
				</option>
				<option value="2">
				  activa
				</option>
				<option value="3">
				  inactiva
				</option>
		  </select>
		</div>

		<div class="form-group has-feedback">
		  {!! Form::label('Periodo') !!}
		  <select type="text" class="form-control" name="periodo">
				<option value="day">
				  Diario
				</option>
				<option value="week">
				  Semanal
				</option>
				<option value="month">
				  Mensual
				</option>
				<option value="year">
				  Anual
				</option>
		  </select>
		</div>

		<div class="form-group has-feedback">
		  {!! Form::label('Latitud') !!}
		  <input id="latitud" type="number" name="latitud" class="form-control" step="any" placeholder="Latitud Propiedad" required/>
		</div>
		
		<div class="form-group has-feedback">
		  {!! Form::label('Longitud') !!}
		  <input id="longitud" type="number" name="longitud" class="form-control" step="any" placeholder="Longitud Propiedad" required/>
		</div>
	  </div>
	</div>
	  </center>
	</div>



	<div class="container-fluid" style="padding-right: 30%; padding-left: 30%;">

  <div class="row" style="margin-right:0;margin-left:0">
	<div class="row text-center">
	  {!! Form::submit(
		'Registrar', 
		array(
		  'class'=>'btn btn-primary btn-lg btn-block'
		 ))
	  !!}    
	</div>
  </div>
</div>

  </div>
</div>
</center> 

</form>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDEkW1BFkszUjGoFG5kSYNksJIgMD1b8K0&libraries=places&callback=initAutocomplete" async defer></script>
@endsection
</div>


