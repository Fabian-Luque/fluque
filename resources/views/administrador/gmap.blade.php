
{!! Html::script('assets/js/jquery-3.2.1.js'); !!}
{!! Html::script('assets/js/bootstrap.min.js'); !!}
{!! Html::style('assets/css/perfil_en_mapa.css'); !!}
{!! $map['js'] !!}

<script type="text/javascript">
  function mostrar(id) {
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
      }
  );
</script>

<script type="text/javascript">
	var centreGot = false;
</script>


{!! $map['js'] !!} 
{!! $map['html'] !!}

<div id="mapa" ></div>

  
<div id="fade" class="overlay"></div>
<div id="light" class="modal">
  <button id="cerrar">x</button>
  <h1>Datos del Hotel</h1>     
</div>