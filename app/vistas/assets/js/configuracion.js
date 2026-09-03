function v_configuracion() {
	$('#formCorreo').validate({
		rules: {
			correoPerfil: {
				required: true,
				email: true
			},
			contrasenaPerfil: {
				required: true,
				//min: 8
			}
		},
		messages: {
			correoPerfil: {
				required: "El email es requerido.",
				email: "El email debe ser valido."
			},
			contrasenaPerfil: {
				required: "La contraseña actual es requerida.",
				min: "La contraseña debe tener al menos 8 caracteres."
			}
		},
		errorClass: 'is-invalid',        
    	errorElement: 'div',
		submitHandler: function (form) {
			// const data = `metodo=modificar&accion=configuracion&tipo=email&email=" + $.trim($("#correoPerfil").val()) + "&contrasena=" + $("#contrasenaPerfil").val()`;
			const data = `metodo=modificar&accion=configuracion&tipo=email&email=${$.trim($('#correoPerfil').val())}&password=${$.trim($('#contrasenaPerfil').val())}`
			$.ajax({
				url: 'index.php',
				type: 'POST',
				data,
				beforeSend: function () {
					$("#carga").show()
				}
			}).done(function (res) {
				if ($.trim(res) === "Correcto") {
					$("#contrasenaPerfil").val("")
					$("#cambioEmail").html($.trim($("#correoPerfil").val()))

					return Swal.fire({
						icon: 'success',
						title: 'El email fue modificado correctamente'
					})

				}
				if ($.trim(res) === "Error Contraseña") {
					return Swal.fire({
						icon: 'warning',
						title: 'Oops...',
						text: 'La contraseña actual es incorrecta.'
					})
				}
				if ($.trim(res).includes('Duplicate entry')) {
					return Swal.fire({
						icon: 'error',
						title: 'Oops...',
						text: 'El email ya ha sido utilizado, por favor intenta con otro.'
					})
				}
				console.log($.trim(res))
				return Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'Error inesperado al modificar el email.'
				})
			}).fail(function () {
				console.log("Error ajax")
			}).always(function () {
				$("#carga").hide()
			})
		}
	});

	$('#formContrasena').validate({
		rules: {
			contrasenaActualPerfil: {
				required: true,
				//min: 8
			},
			nuevaContrasenaPerfil: {
				required: true,
				//min: 8
			},
			repiteContrasenaPerfil: {
				required: true,
				equalTo: "#nuevaContrasenaPerfil"
			}
		},
		messages: {
			contrasenaActualPerfil: {
				required: "La contraseña actual es requerida.",
				min: "La contraseña debe tener al menos 8 caracteres."
			},
			nuevaContrasenaPerfil: {
				required: "La nueva contraseña es requerida.",
				min: "La contraseña debe tener al menos 8 caracteres."
			},
			repiteContrasenaPerfil: {
				required: "Repite la contraseña.",
				equalTo: "Las contraseñas no coinciden."
			}
		},
		errorClass: 'is-invalid',        
    	errorElement: 'div',
		submitHandler: function (form) {
			// var data = "metodo=modificar&accion=perfil&tipo=contrasena&contrasenaA=" + $("#contrasenaActualPerfil").val() + "&contrasena=" + $("#nuevaContrasenaPerfil").val();
			const passwordActual = $.trim($('#contrasenaActualPerfil').val())
			const passwordNueva = $.trim($('#nuevaContrasenaPerfil').val())
			const data = `metodo=modificar&accion=configuracion&tipo=password&passwordActual=${passwordActual}&passwordNueva=${passwordNueva}`
			$.ajax({
				url: 'index.php',
				type: 'POST',
				data,
				beforeSend: function () {
					$("#carga").show();
				}
			}).done(function (res) {
				console.log(res)
				if ($.trim(res) === "Correcto") {
					document.querySelector("#formContrasena").reset()
					return Swal.fire({
						icon: 'success',
						title: 'La contraseña fue modificada correctamente'
					})
				}
				if ($.trim(res) === "Error Contraseña") {
					return Swal.fire({
						icon: 'warning',
						title: 'Oops...',
						text: 'La contraseña actual es incorrecta.'
					})
				}
				console.log($.trim(res))
				return Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'Error inesperado al modificar el email.'
				})
			}).fail(function () {
				console.log("Error ajax")
			}).always(function () {
				$("#carga").hide()
			})
		}
	});

	$('#formPerfilUsuario').validate({
		rules: {
			nombrePerfil: {
				required: true
			},
			primerApellidoPerfil: {
				required: true
			}	
		},
		messages: {
			nombrePerfil: {
				required: "El nombre es requerido."
			},
			primerApellidoPerfil: {
				required: "El apellido es requerido."
			}
		},
		errorClass: 'is-invalid',        
    	errorElement: 'div',
		submitHandler: function (form) {
			const formData = new FormData(document.querySelector("#formPerfilUsuario"));
			formData.append('metodo', 'modificar');
			formData.append('accion', 'configuracion');
			formData.append('tipo', 'foto');
			formData.append('fotoAntes', $("#mosFotoPerfil").attr('foto'));

			$.ajax({
				url: 'index.php',
				type: 'POST',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				beforeSend: function () {
					$("#carga").show();
				}
			}).done(function (res) {
				if ($.trim(res) === "Correcto") {
					Swal.fire({
						icon: 'success',
						title: 'Los datos fueron modificados correctamente'
					});

					$("#bRecargar").trigger('click');

					return
				}
				if ($.trim(res) === 'Error 2 Formato') {
					return Swal.fire({
						icon: 'warning',
						title: 'Oops...',
						text: 'El formato de la foto no está permitido, los formatos permitidos son .png, .jpg o .svg.'
					})
				}
				if ($.trim(res) === 'Error 3 Peso') {
					return Swal.fire({
						icon: 'warning',
						title: 'Oops...',
						text: 'El tamaño de la foto excedió el peso máximo permitido, el peso máximo es de 10MB.'
					})
				}
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'Error inesperado al modificar los datos.'
				});

				console.log($.trim(res));
			}).fail(function () {
				console.log("Error ajax")
			}).always(function () {
				$("#carga").hide()
			});
		}
	});
}

jQuery(document).ready(function ($) {
	$(document).on('change', '#fotoPerfil', function () {
		const tipo = $(this).val().split('.').pop().toLowerCase()
		if (this.files?.[0] == null) return
		if (!['jpeg', 'png', 'jpg', 'svg'].includes(tipo)) {
			$("#fotoPerfil").val("")
			return Swal.fire({
				icon: 'warning',
				title: 'Oops...',
				text: 'El formato de la foto no está permitido, los formatos permitidos son .png, .jpg o .svg'
			})
		}
		if ((this.files?.[0].size / (1024 * 1024)) > 10) {
			$("#fotoPerfil").val("")
			return Swal.fire({
				icon: 'warning',
				title: 'Oops...',
				text: 'El tamaño de la foto excedió el peso máximo permitido, el peso máximo es de 10MB.'
			})
		}
		const reader = new FileReader();

		reader.onload = function (e) {
			$("#mosFotoPerfil").attr('src', e.target.result);
			$('#bCambiarFoto').attr('disabled', false);
		}

		reader.readAsDataURL(this.files[0]);
	});

	$(document).on('change keyup', '#formPerfilUsuario input', function() {
		$('#bCambiarFoto').attr('disabled', false);
	});

	$(document).on('click', '#bQuitarFoto', function () {
		$("#fotoPerfil").val("");
		$("#mosFotoPerfil").attr('src', 'vistas/assets/images/producto-generico.jpg');
		$('#bCambiarFoto').attr('disabled', true);
	});

	$(document).on('click', '#bContraPerfil', function () {
		if ($(this).children('i').hasClass('fa-eye')) {
			$(this).children('i').removeClass('fa-eye');
			$(this).children('i').addClass('fa-eye-slash');

			$("#contrasenaPerfil").attr('type', 'text');
		} else {
			$(this).children('i').removeClass('fa-eye-slash');
			$(this).children('i').addClass('fa-eye');

			$("#contrasenaPerfil").attr('type', 'password');
		}
	});

	$(document).on('click', '#bContraCamCon', function () {
		if ($(this).children('i').hasClass('fa-eye')) {
			$(this).children('i').removeClass('fa-eye');
			$(this).children('i').addClass('fa-eye-slash');

			$("#contrasenaActualPerfil").attr('type', 'text');
		} else {
			$(this).children('i').removeClass('fa-eye-slash');
			$(this).children('i').addClass('fa-eye');

			$("#contrasenaActualPerfil").attr('type', 'password');
		}
	});

	$(document).on('click', '#bContraCamDos', function () {
		if ($(this).children('i').hasClass('fa-eye')) {
			$(this).children('i').removeClass('fa-eye');
			$(this).children('i').addClass('fa-eye-slash');

			$("#nuevaContrasenaPerfil").attr('type', 'text');
			$("#repiteContrasenaPerfil").attr('type', 'text');
		} else {
			$(this).children('i').removeClass('fa-eye-slash');
			$(this).children('i').addClass('fa-eye');

			$("#nuevaContrasenaPerfil").attr('type', 'password');
			$("#repiteContrasenaPerfil").attr('type', 'password');
		}
	});
});