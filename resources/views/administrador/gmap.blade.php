
{!! Html::script('assets/js/jquery-3.2.1.js'); !!}
{!! Html::script('assets/js/bootstrap.min.js'); !!}
{!! Html::style('assets/css/perfil_en_mapa.css'); !!}

<div id="js"></div>

<script type="text/javascript">
  function mostrar() {
      document.getElementById('light').style.display='block';
      document.getElementById('fade').style.display='block';
  }
  $(document).ready(
      function(e) {
          $("#cerrar").click(
            function(e) {
                document.getElementById('light').style.display='none';
                document.getElementById('fade').style.display='none';
            }
          );

              $.ajax({
            type: 'POST',
            url:  'http://demo-api.gofeels.com/hoteles/cercanos',
            headers: {'X-CSRF-TOKEN': "<?php echo csrf_token(); ?>"},
            data: "",
                  
            success: function(data) {
            
              console.log(data);
              $("#js").append("echo "+data.mapa.js);
              $("#mapa").append(data.mapa.html);
            },
          error: function(xhr, textStatus, thrownError) {

            }
        });


          



      }
  );
</script>

<script type="text/javascript">
	var centreGot = false;
</script>

<script type="text/javascript">
  
</script>


<div id="mapa" ></div>
  
<div id="fade" class="overlay"></div>
<div id="light" class="modal">
  <button id="cerrar">x</button>
  <h1>Datos del Hotel</h1>     
</div>