jQuery(document).ready(function ($) {
	// validate registry form

	$('#formLogin').validate({
		rules: {
			email: {
				required: true,
				email: true
			},
			pass: {
				required: true
			}
		},
		messages: {
			email: {
				required: "El correo electrónico es requerido.",
				email: "Introduce un correo electrónico válido."
			},
			pass: {
				required: "La contraseña es requerida"
			}
		},
		errorClass: 'is-invalid',
		errorElement: 'div',
		submitHandler: function (form) {
			var data = "accion=login&correo=" + $.trim($("#email").val()) + "&contrasena=" + $("#pass").val();

			$.ajax({
				url: 'index.php',
				type: 'POST',
				data: data,
				beforeSend: function () {
					$("#mensaAV").html(`<div class="alert alert-primary alert-dismissible fade show" role="alert">
						<i class="fas fa-info-circle"></i> <strong>Cargando . . .</strong>
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>`);
					$("#mensaAV").show();
					$("#bIngresarLogin").prop('disabled', true);
					$("#bIngresarLogin").html('Ingresar <div class="spinner-border text-light" role="status"></div>');
				}
			})
				.done(function (res) {
					if ($.trim(res) == "Correcto") {
						setTimeout(function () {
							$("#mensaAV").html(`<div class="alert alert-success alert-dismissible fade show" role="alert">
								<i class="fas fa-check-circle"></i> <strong>Accediendo . . .</strong>
								<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
							</div>`);

							setTimeout(function () {
								window.location.reload();
							}, 1000);
						}, 1500);
					} else if (res.trim() == "No existe") {
						setTimeout(function () {
							$("#mensaAV").html(`<div class="alert alert-danger alert-dismissible fade show" role="alert">
								<i class="fas fa-exclamation-triangle"></i> <strong>El correo electrónico no está registrado; por favor utiliza un correo válido o crea una cuenta.</strong>
								<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
							</div>`);
						}, 1500);
					} else if ($.trim(res) == "0") {
						setTimeout(function () {
							$("#mensaAV").html(`<div class="alert alert-danger alert-dismissible fade show" role="alert">
								<i class="fas fa-exclamation-triangle"></i> <strong>Usuario o contraseña incorrectos.</strong>
								<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
							</div>`);
						}, 1500);
					} else if ($.trim(res) == "Supero Intentos") {
						setTimeout(function () {
							$("#mensaAV").html(`<div class="alert alert-warning alert-dismissible fade show" role="alert">
								<i class="fas fa-exclamation-triangle"></i> <strong>Superaste el número de intentos.</strong> Espera 15 minutos y vuelve a intentar.
								<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
							</div>`);
						}, 1500);
					} else {
						setTimeout(function () {
							$("#mensaAV").html(`<div class="alert alert-danger alert-dismissible fade show" role="alert">
								<i class="fas fa-exclamation-triangle"></i> <strong>Error Inesperado.</strong>
								<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
							</div>`);
						}, 1500);

						console.log($.trim(res));
					}
				})
				.fail(function () {
					console.log("Error ajax");
				})
				.always(function () {
					setTimeout(function () {
						$("#bIngresarLogin").prop('disabled', false);
						$("#bIngresarLogin").html('Ingresar <i class="fas fa-check"></i>');
					}, 2000);
				});
		}
	});

	$('#formReContra').validate({
		rules: {
			reEmail: {
				required: true,
				email: true
			}
		},
		messages: {
			reEmail: {
				required: "El correo electrónico es requerido.",
				email: "Introduce un correo electrónico válido."
			}
		},
		errorClass: 'is-invalid',
		errorElement: 'div',
		submitHandler: function (form) {
			var data = "accion=olvido&correo=" + $.trim($("#reEmail").val());

			$.ajax({
				url: 'index.php',
				type: 'POST',
				data: data,
				beforeSend: function () {
					$("#bAceReCo").prop('disabled', true);
					$("#bCanReCo").prop('disabled', true);
					$("#bAceReCo").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Aceptar');
				}
			})
				.done(function (res) {
					if ($.trim(res) == "Correcto") {
						Swal.fire({
							icon: 'success',
							title: 'Se ha cambiado tu contraseña a una temporal',
							text: 'Por favor revisa tu cuenta de correo electrónico, tu contraseña ha sido enviada a él. La próxima vez que accedas a VentasTool deberás cambiarla.',
							footer: 'Si la contraseña no ha llegado a tu cuenta de correo inténtalo de nuevo o contáctanos en ventastool@bigtool.mx'
						});

						document.getElementById("formReContra").reset();
						$("#modalOlvidoContra").modal('hide');
					} else if ($.trim(res) == "Error 3 No existe") {
						Swal.fire({
							icon: 'warning',
							title: 'Oops...',
							text: 'El correo electrónico que has ingresado no existe o ha sido bloqueado.',
							footer: '¿Por qué tengo este error? Contáctanos en ventastool@bigtool.mx'
						}).then((result) => {
							$("#reEmail").focus();
						});
					} else {
						Swal.fire({
							icon: 'error',
							title: 'Oops...',
							text: 'Error inesperado al recuperar tu contraseña.',
							footer: '¿Por qué tengo este error? Contáctanos en ventastool@bigtool.mx'
						});
						console.log($.trim(res));
					}
				})
				.fail(function () {
					console.log("Error ajax");
				})
				.always(function () {
					$("#bAceReCo").prop('disabled', false);
					$("#bCanReCo").prop('disabled', false);
					$("#bAceReCo").html('<i class="fa fa-paper-plane"></i> Aceptar');
				});
		}
	});
});