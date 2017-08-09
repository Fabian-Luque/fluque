@section('scripts')
<script type="text/javascript">
	function InfoModal(titulo, texto) {
		$("#titulomodal").empty();
		$("#titulomodal").append("<p>"+titulo+"</p>");
		$("#textmodal").empty();
		$("#textmodal").append("<p>"+texto+"</p>");
		$("#confirma-del").hide();
		$('#myModal').modal('show');

	}

	function MisRequests(tipo, ur, tok, accion, datos) {
		$.ajax({
            type: tipo,
            url:  ur,
            headers: {'X-CSRF-TOKEN': tok},
            data: datos,
            			
            success: function(data) {
				InfoModal(
					accion,
					data.msg
				);
            },
        	error: function(xhr, textStatus, thrownError) {
            	InfoModal(
					accion,
					textStatus
				);
            }
        });
	}

	$(document).ready(
		function(e) {

			$("#confirmamodal").click(
				function(e) {
					e.preventDefault();	
					location.reload();
				}
			);
			
			$("#confirma-del").click(
				function(e) {
					e.preventDefault();
					var id = $('#confirma-del').attr('value');
					var ur = "<?php echo url('dash/eliminar/user'); ?>";
					var tok = "<?php echo csrf_token(); ?>";	
	
					MisRequests(
						"POST",
						ur,
						tok,
						"Eliminar usuario",
						{id: id, _token: tok}
					);


				}
			);

			$("#btn-crear").click(
				function(e) {
					e.preventDefault();
			
					window.location.replace(
						"<?php echo url('/dash/adminreguser');?>"
					);
				}
			);

			$("a").click(
				function(e) {
					if ($(this).attr('name') == 'b-lista') {
						e.preventDefault();
						var val = $(this).attr('href');

						switch (val) {
        					case 'u':
        						InfoModal("Actualizar","datos");
        						$("#textmodal").load(
        							"<?php echo url('/dash/edituser');?>"
        						);
        					break;

        					case 'd':
        						var row = $(this).parents('tr');
								var id = row.data('id');
        						
        						InfoModal(
									"Confirmacion",
									"<h3>¿Esta seguro que desea eliminar este registro?</h3>"
								);
								$('#confirma-del').attr('value', id);
								$('#confirma-del').show();
        					break;
        				
        					default: 
        					break;
						}// fin switch
					} 
				}// fin funcion
			);				
		}
	);
</script>
@endsection



