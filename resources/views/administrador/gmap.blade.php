
{!! Html::script('assets/js/jquery-3.2.1.js'); !!}
{!! Html::script('assets/js/bootstrap.min.js'); !!}
{!! Html::style('assets/css/perfil_en_mapa.css'); !!}

<script type="text/javascript">
  $(document).ready(
      function(e) {
          $("#cerrar").click(
            function(e) {
                document.getElementById('light').style.display='none';
                document.getElementById('fade').style.display='none';
            }
          );
      }
  );
</script>

<script type="text/javascript">
	var centreGot = false;
</script>

<script type="text/javascript">
  
</script>
	{!! $map['js'] !!} 




{!! $map['html'] !!}


<script type="text/javascript">
    var geocoder = new google.maps.Geocoder();

    address = document.getElementById('direccion').value;
    
    if(address != '') {
  // Llamamos a la función geodecode pasandole la dirección que hemos introducido en la caja de texto.
      geocoder.geocode({ 
          'address': address
        }, 
        function(results, status) {
            if (status == 'OK') {
                // Mostramos las coordenadas obtenidas en el p con id coordenadas
                alert(results[0].geometry.location.lat()+', '+results[0].geometry.location.lng());

                mapa.marker.setPosition(results[0].geometry.location);
// Centramos el mapa en las coordenadas obtenidas
                mapa.map.setCenter(mapa.marker.getPosition());
                agendaForm.showMapaEventForm();
            }
        }
      );
    }

</script>


  
<div id="fade" class="overlay"></div>
<div id="light" class="modal">
  <button id="cerrar">x</button>
  <h1>Datos del Hotel</h1>     
</div>