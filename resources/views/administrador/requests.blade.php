
@section('scripts')

<script type="text/javascript">
	function InfoModal(titulo, texto) {
		$("#titulomodal").empty();
		$("#titulomodal").append("<p>"+titulo+"</p>");
		$("#textmodal").empty();
		$("#textmodal").append("<p>"+texto+"</p>");
		$('#myModal').modal('show');
	}

	function MisRequests(tipo, ur, tok, accion, datos) {
		$.ajax({
            type: tipo,
            url:  ur,
            headers: {'X-CSRF-TOKEN': tok},
            data: datos,
            			
            success: function(data) {
				$("#titulomodal").empty();
				$("#titulomodal").append("<p>"+accion+"</p>");
				$("#textmodal").empty();
				$("#textmodal").append("<p>"+data.msg+"</p>");
				$('#myModal').modal('show');
            },
        	error: function(xhr, textStatus, thrownError) {
            	$("#titulomodal").empty();
				$("#titulomodal").append("<p> Error </p>");
				$("#textmodal").empty();
				$("#textmodal").append("<p>"+textStatus+"</p>");
				$('#myModal').modal('show');
            }
        });
	}

	$(document).ready(
		function(e) {
			$("#btn-crear").click(
				function(e) {
					e.preventDefault();
					var row = $(this).parents('tr');
					var id = row.data('id');

					InfoModal("holaa","chaooo");
					window.location.replace(
						"<?php echo url('/dash/adminreguser');?>"
					);
				}
			);

			$("#btn-editar-lista").click(
				function(e) {
					e.preventDefault();
					var row = $(this).parents('tr');
					var id = row.data('id');

					InfoModal("holaa","chaooo");
				}
			);

			$("#btn-eliminar-lista").click(
				function(e) {
					e.preventDefault();
					var row = $(this).parents('tr');
					var id = row.data('id');
					var ur = "<?php echo url('/dash/eliminar/user') ?>";
					var tok = "<?php echo csrf_token(); ?>";		
	
					MisRequests(
						"POST",
						ur,
						tok,
						"Eliminar usuario",
						{id: id, _token: tok}
					)

					var style = document.styleSheets[0];
            		style.removeRule (0);
					var tabla = document.getElementById("#tablausuarios");
            		tabla.refresh ();
				}
			);
				
		}
	);
</script>
@endsection





