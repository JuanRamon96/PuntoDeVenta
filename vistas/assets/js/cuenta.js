function v_cuenta() {
	$('#formConfiguracionNegocio').validate({
        rules: {
        	/*nombreNegocio: {
        		required: true
        	}*/
        },
        messages: {
        	nombreNegocio: {
        		required: "El nombre es requerido."
        	}
        },
        submitHandler: function(form) {
			var formData = new FormData(document.getElementById("formConfiguracionNegocio"));
			formData.append('metodo', 'insertar');
			formData.append('accion', 'cuenta');

			$.ajax({
				url: 'index.php',
				type: 'POST',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				beforeSend: function() {
					$("#carga").show();
				}
			})
			.done(function(res) {
				if ($.trim(res) == "Correcto") {
					Swal.fire({
					    icon: 'success',
					    title: 'Los datos fueron modificados correctamente'
					});

					$("#bCancelarFotoNegocio").addClass('oculto');
	    			$("#bModificarFotoNegocio").removeClass('oculto');
				}else if($.trim(res) == 'Error 2 Formato'){
					Swal.fire({
					    icon: 'warning',
					    title: 'Oops...',
					    text: 'El formato de la foto no está permitido, los formatos permitidos son .png, .jpg o .svg.'
					});
				}else if($.trim(res) == 'Error 3 Peso') {
					Swal.fire({
					    icon: 'warning',
					    title: 'Oops...',
					    text: 'El tamaño de la foto excedió el peso máximo permitido, el peso máximo es de 10MB.'
					});
				}else{
					Swal.fire({
					    icon: 'error',
					    title: 'Oops...',
					    text: 'Error inesperado al modificar los datos.'
					});

					console.log($.trim(res));
				}
			})
			.fail(function() {
				console.log("Error ajax");
			})
			.always(function() {
				$("#carga").hide();
			});	       
        }
    });	
}

jQuery(document).ready(function($) {
	
	$(document).on('click', '#bCancelarFotoNegocio', function () {
	    $(this).addClass('oculto');
	    $("#bModificarFotoNegocio").removeClass('oculto');
	    $('#fotoNegocio').val('');
	    $("#mosFotoNegocio").css('background-image', $(this).attr('foto'));
	    $("#mosFotoNegocio").parent().attr('href', $(this).attr('foto').replace('url(', '').replace(')', '').replace(/\"/gi, ""));
	});

  	$(document).on('click', '#bModificarFotoNegocio', function () {
	    $('#fotoNegocio').trigger('click');
  	});

  	$(document).on('change', '#fotoNegocio', function () {
	    var tipo = $(this).val().substring($(this).val().lastIndexOf(".")).toLowerCase();

	    if (this.files && this.files[0]) {
	        if (tipo != ".jpeg" && tipo != ".png" && tipo != ".jpg" && tipo != ".svg") {
		        Swal.fire({
		            icon: 'warning',
		            title: 'Oops...',
		            text: 'El formato de la foto no está permitido, los formatos permitidos son .png, .jpg o .svg'
		        });
	        } else if ((this.files[0].size / (1024 * 1024)) > 10) {
		        Swal.fire({
		            icon: 'warning',
		            title: 'Oops...',
		            text: 'El tamaño de la foto excedió el peso máximo permitido, el peso máximo es de 10MB.'
		        });
	        } else {
		        $("#bCancelarFotoNegocio").attr('foto', $("#mosFotoNegocio").css('background-image'));
		        $("#bModificarFotoNegocio").addClass('oculto');
		        $("#bCancelarFotoNegocio").removeClass('oculto');

		        var reader = new FileReader();

		        reader.onload = function (e) {
		            $("#mosFotoNegocio").css('background-image', "url('" + e.target.result + "')");
		            $("#mosFotoNegocio").parent().attr('href', e.target.result);
		        }

		        reader.readAsDataURL(this.files[0]);
	        }
	    }
	});
});