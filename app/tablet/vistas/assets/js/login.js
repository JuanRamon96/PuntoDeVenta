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
				required: "El correo electrónico es requerido",
				email: "Introduce un correo electrónico válido"
			},
			pass: {
				required: "La contraseña es requerida"
			}
		},
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
});