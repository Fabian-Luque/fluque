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
			$("#btn-buscar").click(
				function(e) {
					e.preventDefault();
			
					window.location.replace(
						"<?php echo url('/dash/buscauser');?>"
					);
				}
			);

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
        						var row = $(this).parents('tr');
								var id = row.data('id');
								var tok = "<?php echo csrf_token(); ?>";
								var user;
						
								
        						$("#textmodal").load(
        							"<?php echo url('/dash/edituser');?>"
        						);
								$.ajax({
            						type: "POST",
            						url:  "<?php echo url('dash/obtener/user'); ?>",
            						headers: {
            							'X-CSRF-TOKEN': tok
            						},
            						data: {
            							id: id, 
            							_token: tok
            						},
            			
            						success: function(data) {
            							console.log(data.msg.propiedad[0].nombre);
            							console.log(data.msg);
            							$("#id_user").val(data.msg.id);
            							$("#name").val(data.msg.name);
            							$("#email").val(data.msg.email);
            							$("#password").val(data.msg.password);
            							$("#phone").val(data.msg.phone);
            							

										$("#num_hab").val(data.msg.propiedad[0].numero_habitaciones);
            							$("#nombre").val(data.msg.propiedad[0].nombre);
            							$("#ciudad").val(data.msg.propiedad[0].ciudad);
            							$("#direccion").val(data.msg.propiedad[0].direccion);
            							$("#estado_cuenta").val(
            								data.msg.propiedad[0].estado_cuenta_id
            							);
            							
            						},
        							error: function(xhr, textStatus, thrownError) {
            							user = 'false';
            						}
        						});

        						InfoModal("Actualizar",".");
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
        				
          					case 'up':
        						InfoModal("Actualizar","datos");
        						$("#textmodal").load(
        							"<?php echo url('/dash/edituser');?>"
        						);
        					break;

        					case 'dp':
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



