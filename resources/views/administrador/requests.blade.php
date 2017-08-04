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
				InfoModal(
					accion,
					data.msg
				);
            },
        	error: function(xhr, textStatus, thrownError) {
            	InfoModal(
					accion,
					xhr.responseText
				);
            }
        });
	}

	$(document).ready(
		function(e) {
			$("form[name='f-crear-user']").submit(
				function(e) {
					e.preventDefault();
			    	var form 	 = $(e.target);
                	var	formData = new FormData();
                	var	params   = form.serializeArray();
                	
	            	$.each(
	            		params, 
	            		function(i, val) {
        	        		formData.append(
        	        			val.name, 
        	        			val.value
        	        		);
    	        		}
    	        	);
    	  
    	        	var url = "<?php echo url('');?>";//form.attr('id');

	            	MisRequests(
    	        		'POST',
        	    		url + form.attr('id'),
        	    		formData.get('_token'),
        	    		'Registrar nuevo usuario',
        	    		params
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
        										InfoModal(
					"Actualizar datos",
					"<button>Actualizar</button>"
				);
        					break;

        					case 'd':
        						var row = $(this).parents('tr');
								var id = row.data('id');
								var ur = "<?php echo url('dash/eliminar/user'); ?>";
								var tok = "<?php echo csrf_token(); ?>";	
	
								MisRequests(
									"POST",
									ur,
									tok,
									"Eliminar usuario",
									{id: id, _token: tok}
								);
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

