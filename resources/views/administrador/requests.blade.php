@section('scripts')

<script type="text/javascript">
	function InfoModal(titulo, texto) {
		$("#titulomodal").empty();
		$("#titulomodal").append("<p>"+titulo+"</p>");
		$("#textmodal").empty();
		$("#textmodal").append("<p>"+texto+"</p>");
		$('#myModal').modal('show');
	}

	function objectifyForm(formArray) {//serialize data function
  		var returnArray = {};
  		for (var i = 0; i < formArray.length; i++){
    		returnArray[formArray[i]['name']] = formArray[i]['value'];
  		}
  		return returnArray;
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
        	    		{id: "4", _token: formData.get('_token')}
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
					var ur = "<?php echo url('dash/eliminar/user'); ?>";
					var tok = "<?php echo csrf_token(); ?>";	

					alert(id+'  '+tok+ '  '+ ur);	
	
					MisRequests(
						"POST",
						ur,
						tok,
						"Eliminar usuario",
						{id: id, _token: tok}
					)

					var style = document.styleSheets[0];
            		style.removeRule(0);
					var tabla = document.getElementById("#tablausuarios");
            		tabla.refresh();

				}
			);
				
		}
	);
</script>
@endsection





